<?php
namespace Nubersoft;

class nForm extends \Nubersoft\nApp
{
    protected $nform_settings,
        $NBR_ROOT_DIR,
        $labelWrap;

    public function __construct($dir = false)
    {
        # Create default root
        $this->setObserverRootDir($dir);
        # Set the label as wrapped
        $this->labelPos();

        return parent::__construct();
    }

    public function createForm($array, $openAttr = null)
    {

        echo $this->open($openAttr);
        foreach ($array as $attr) {
            if (!isset($attr['html'])) {
                $type = (!empty($attr['type'])) ? $attr['type'] : 'text';
                if (isset($attr['type']))
                    unset($attr['type']);

                echo $this->{$type}($attr) . PHP_EOL;
            } else {
                echo $attr['html'];
            }
        }
        echo $this->close();
    }

    public function labelPos($val = true)
    {
        $this->labelWrap = (!empty($val));
        return $this;
    }

    public function setObserverRootDir($dir)
    {
        $this->NBR_ROOT_DIR = (!empty($dir)) ? $dir : __DIR__ . DS . 'nForm';
    }

    protected function resetSettings()
    {
        return array(
            'value' => false,
            'name' => false,
            'id' => false,
            'class' => false,
            'size' => false,
            'class' => false,
            'options' => false,
            'style' => false,
            'placeholder' => false,
            'label' => false,
            'selected' => false,
            'disabled' => false,
            'other' => false
        );
    }

    private function imploder($array = false, $use = ';', $format = false)
    {
        if (is_array($array))
            return implode($use, $array);

        return ($format) ? json_encode($array) : $array;
    }

    private function useWrapper($data, $type = false)
    {
        return (!empty($type)) ? " {$type}=\"{$data}\"" : $data;
    }

    protected function processSettings($settings = false)
    {
        $thisObj = $this;
        $default = (!empty($settings['default'])) ? preg_replace_callback('/[^:]{1,}[:]{2}[^:]{1,}/', function ($v) use ($thisObj) {
            $exp = explode('::', $v[0]);
            switch ($exp[0]) {
                case ('SESSION'):
                    return (isset($_SESSION[$exp[1]])) ? $_SESSION[$exp[1]] : $v[0];
                case ('POST'):
                    return (!empty($thisObj->getPost($exp[1]))) ? $thisObj->getPost($exp[1]) : $v[0];
                case ('GET'):
                    return (!empty($thisObj->getGet($exp[1]))) ? $thisObj->getGet($exp[1]) : $v[0];
                case ('REQUEST'):
                    return (!empty($thisObj->getRequest($exp[1]))) ? $thisObj->getRequest($exp[1]) : $v[0];
                case ('SERVER'):
                    return (isset($_SERVER[$exp[1]])) ? $_SERVER[$exp[1]] : $v[0];
                case ('FUNC'):
                    return (function_exists($exp[1])) ? $exp[1]() : $v[0];
            }
        }, trim($settings['default'], '~')) : '';

        $class = (!empty($settings['class'])) ? $this->useWrapper($this->imploder($settings['class'], ' '), 'class') : false;
        $name = (!empty($settings['name'])) ? $settings['name'] : false;
        $size = (!empty($settings['size'])) ? $settings['size'] : false;
        $label = (!empty($settings['label'])) ? $settings['label'] : false;

        $value = (!empty($settings['value'])) ? $settings['value'] : $default;
        $options = (!empty($settings['options'])) ? $settings['options'] : array(array('', 'Select', true));
        $id = (!empty($settings['id'])) ? $this->useWrapper($settings['id'], 'id') : false;
        $type = (!empty($settings['type'])) ? $settings['type'] : 'text';
        $style = (!empty($settings['style'])) ? $this->useWrapper($this->imploder($settings['style']), 'style') : false;
        $placeholder = (!empty($settings['placeholder'])) ? $this->useWrapper($settings['placeholder'], 'placeholder') : false;
        $selected = (!empty($settings['selected'])) ? ' selected' : false;
        $select = (!empty($settings['select'])) ? $settings['select'] : false;
        $disabled = (!empty($settings['disabled'])) ? ' disabled' : false;
        $other = (!empty($settings['other'])) ? ' ' . $this->imploder($settings['other'], ' ') : false;
        $wrap_class = (!empty($settings['wrap_class'])) ? ' ' . $this->imploder($settings['wrap_class'], ' ') : false;

        $this->nform_settings = [
            'name' => $name,
            'value' => $value,
            'id' => $id,
            'class' => $class,
            'wrap_class' => $wrap_class,
            'size' => $size,
            'class' => $class,
            'options' => $options,
            'style' => $style,
            'placeholder' => $placeholder,
            'label' => $label,
            'selected' => $selected,
            # This one you can force the value to be matched in a dropdown
            'select' => $select,
            'disabled' => $disabled,
            'other' => $other
        ];
    }

    protected function includeFile($file)
    {
        if (is_file($file)) {
            ob_start();
            include($file);
            $this->nform_settings = $this->resetSettings();
            $data = ob_get_contents();
            ob_end_clean();
            return $data;
        } else {
            return '<!--Form file type does not exist: ' . $this->safe()->encodeSingle($file) . ' OR field name is empty.-->';
            //throw new \Exception();
        }
    }

    protected function getType($type)
    {
        return $this->NBR_ROOT_DIR . DS . $type . DS . 'index.php';
    }

    protected function useLayout($settings, $type, $layout = 'std')
    {
        # process the settings
        $this->processSettings($settings);
        switch ($layout) {
            case ('mod'):
                if ($this->nform_settings['size'])
                    $this->nform_settings['size'] = ' size="' . $this->nform_settings['size'] . '"';
                break;
            case ('chk'):
                if ($this->nform_settings['selected'])
                    $this->nform_settings['selected'] = ' checked';
                break;
        }

        try {
            # Include the file
            return $this->includeFile($this->getType($type));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($this->getHelper('UserEngine')->isAdmin())
                die($msg);
            else
                return '<!--' . $msg . '-->';
        }
    }
    /**
     * @description This method will take a full array of data that make up an entire form and use it
     * to build single inputs
     * @param $settings [array{forced}] This is all the required settings to build a form input
     */
    public function multiValueDeterminer(array $settings)
    {
        if (empty($settings)) {
            throw self::getClass('nException', 'Settings can not be empty.');
            return false;
        }
        # Type will attempt a dynamic method call
        $type = (!empty($settings['type'])) ? $settings['type'] : 'text';
        $values = (!empty($settings['value'])) ? $settings['value'] : false;
        $name = (!empty($settings['name'])) ? $settings['name'] : 'name';
        $size = (!empty($settings['size'])) ? $settings['size'] : false;
        $dropdowns = (!empty($settings['options'])) ? $settings['options'] : array();
        $placeholder = (!empty($settings['placeholder'])) ? $settings['placeholder'] : false;
        $label = (!empty($settings['label'])) ? $settings['label'] : false;
        $other = (!empty($settings['other'])) ? $settings['other'] : false;
        $class = (!empty($settings['class'])) ? $settings['class'] : false;
        $wrap_class = (!empty($settings['wrap_class'])) ? $settings['wrap_class'] : false;
        $style = (!empty($settings['style'])) ? $settings['style'] : false;

        if (is_array($values)) {
            $values = $this->toArray($values);
        }

        if (strpos($name, '[') !== false) {
            $sColumn = explode('[', $name);
            $sColumn = array_map(function ($v) {
                return str_replace(']', '', $v);
            }, $sColumn);
            $new = array();
            $findArr = $this->getMatchedArray($sColumn, '', $values);
            $curr = end($sColumn);
            $values[$name] = (isset($findArr[$curr][0])) ? $findArr[$curr][0] : false;
        }
        $column = $name;
        # Set form input options
        $opts = array(
            'value' => ((!empty($values[$name])) ? $values[$name] : ''),
            'name' => $name,
            'options' => ((!empty($dropdowns[$name])) ? $dropdowns[$name] : false),
            'placeholder' => $placeholder,
            'label' => $label,
            'size' => $size,
            'other' => $other,
            'class' => $class,
            'wrap_class' => $wrap_class,
            'style' => $style
        );

        # If this is a select
        if ($type == 'select' || $type == 'radio') {
            # Get the column name
            $colName = $opts['options'][0]['assoc_column'];
            # Add a select button
            $blank = array_unshift($opts['options'], array(
                'assoc_column' => $colName,
                'menuName' => 'Select',
                'menuVal' => ''
            ));
            # Loop through the options and replace keys
            foreach ($opts['options'] as $key => $val) {
                $currVal = (isset($val['menuVal'])) ? $val['menuVal'] : $val['value'];
                if ($opts['value'] == $currVal) {
                    $opts['options'][$key]['selected'] = true;
                }
                # Replace non-standard keys with new ones
                $this->replaceKeys($opts['options'][$key], array('name' => 'menuName', 'value' => 'menuVal'));
            }
        }
        # Renders the form element
        return $this->{$type}($opts);
    }
    /**
     * @description This method will take a full array of data that make up an entire form and use it
     * to build single inputs
     * @param $settings [array{forced}] This is all the required settings to build a form input
     */
    public function multiForm(array $settings)
    {
        if (empty($settings)) {
            throw self::getClass('nException', 'Settings can not be empty.');
            return false;
        }
        # Type will attempt a dynamic method call
        $type = (!empty($settings['type'])) ? $settings['type'] : 'text';
        $values = (!empty($settings['value'])) ? $settings['value'] : false;
        $name = (!empty($settings['name'])) ? $settings['name'] : 'name';
        $size = (!empty($settings['size'])) ? $settings['size'] : false;
        $dropdowns = (!empty($settings['options'])) ? $settings['options'] : array();
        $placeholder = (!empty($settings['placeholder'])) ? $settings['placeholder'] : false;
        $label = (!empty($settings['label'])) ? $settings['label'] : false;
        $other = (!empty($settings['other'])) ? $settings['other'] : false;
        $class = (!empty($settings['class'])) ? $settings['class'] : false;
        $wrap_class = (!empty($settings['wrap_class'])) ? $settings['wrap_class'] : false;
        $style = (!empty($settings['style'])) ? $settings['style'] : false;

        if (is_array($values)) {
            $values = $this->toArray($values);
        }

        if (strpos($name, '[') !== false) {
            $sColumn = explode('[', $name);
            $sColumn = array_map(function ($v) {
                return str_replace(']', '', $v);
            }, $sColumn);
            $new = array();
            $findArr = $this->getMatchedArray($sColumn, '', $values);
            $curr = end($sColumn);
            $values[$name] = (isset($findArr[$curr][0])) ? $findArr[$curr][0] : false;
        }
        $column = $name;
        # Set form input options
        $opts = array(
            'value' => ((!empty($values[$name])) ? $values[$name] : $values),
            'name' => $name,
            'options' => ((!empty($dropdowns)) ? $dropdowns : false),
            'placeholder' => $placeholder,
            'label' => $label,
            'size' => $size,
            'other' => $other,
            'class' => $class,
            'wrap_class' => $wrap_class,
            'style' => $style
        );

        # If this is a select
        if ($type == 'select' || $type == 'radio') {
            $hasSelect = false;

            # Loop through the options and replace keys
            foreach ($opts['options'] as $key => $row) {

                if (empty($row['value']) && $type == 'select')
                    $hasSelect = true;

                if ($opts['value'] == $row['value']) {
                    $opts['options'][$key]['selected'] = true;
                }
            }

            if (!$hasSelect && $type == 'select') {
                $opts['options'] = array_merge([['name' => 'Select', 'value' => '']], $opts['options']);
            }
        }

        # Renders the form element
        return $this->{$type}($opts);
    }

    public function open(array $settings = null, $quotes = false)
    {
        $settings['action'] = (!empty($settings['action'])) ? $settings['action'] : '#';
        $settings['method'] = (!empty($settings['method'])) ? $settings['method'] : 'post';
        $quotes = (empty($quotes)) ? '"' : "'";

        foreach ($settings as $attr => $val) {
            if ($attr == 'other')
                continue;

            if (is_array($val))
                continue;

            $options[] = $attr . '=' . $quotes . $val . $quotes;
        }

        if (!empty($settings['other']))
            $options[] = (is_array($settings['other'])) ? implode(' ', $settings['other']) : $settings['other'];

        ob_start();
        include(__DIR__ . DS . 'nForm' . DS . 'form' . DS . 'open.php');
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public function close()
    {
        ob_start();
        include(__DIR__ . DS . 'nForm' . DS . 'form' . DS . 'close.php');
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public function __call($name, $args = false)
    {
        $name = strtolower($name);
        $kind['checkbox'] = 'chk';
        $kind['text'] = 'mod';
        $kind['password'] = 'mod';
        $mod = (isset($kind[$name])) ? $kind[$name] : 'std';
        $arg = (isset($args[0])) ? $args[0] : false;
        return $this->useLayout($arg, $name, $mod);
    }

    public static function __callStatic($name, $args = false)
    {
        $method = str_replace('get', '', strtolower($name));
        $args = (is_array($args)) ? $args : [$args];
        return (new nForm())->{$method}(...$args);
    }

    public function repSelectOptions($options, $replace = array('name' => 'menuName', 'value' => 'menuVal'))
    {
        foreach ($options as $key => $option) {
            $this->replaceKeys($options[$key], $replace);
        }

        return $options;
    }

    public function setIsSelected($options, $value, $matchto = 'name')
    {
        foreach ($options as $key => $option) {
            if ($value == $options[$key][$matchto]) {
                $options[$key]['selected'] = true;
            }
        }
        return $options;
    }

    public function formatSelectOptions($options, $value = false, $replace = false)
    {
        # Get the column name
        $getAssoc = $this->getMatchedArray(array('assoc_column'), '', $options);
        # If none set, stop
        if (!isset($getAssoc['assoc_column']))
            return array();
        # Create a select menu option
        array_unshift($options, array('assoc_column' => $getAssoc['assoc_column'][0], 'menuName' => 'Select', 'menuVal' => '', 'page_live' => 'on'));
        # See if there is a different replacement scheme
        $replace = (is_array($replace)) ? $replace : false;
        # Replace the keys
        $options = ($replace) ? $this->repSelectOptions($options, $replace) : $this->repSelectOptions($options);
        # Organize and determine selected
        $options = $this->setIsSelected($options, $value);
        //echo printpre($options,array('backtrace'=>false,'line'=>__LINE__));
        return $options;
    }
    /**
     * @description Takes a string (from a parsed jquery form by default) and turns it to array
     */
    public function deliverToArray($string = false)
    {
        if (empty($string) && !empty($this->getPost('deliver')['formData']))
            $string = $this->getPost('deliver')['formData'];

        $parsed = array();
        $data = $this->safe()->decode($string);
        parse_str($data, $parsed);

        return $parsed;
    }
    /**
     *	@description	
     */
    public static function getOptions(string $assoc_column, $selected = false)
    {
        $def = [
            [
                'name' => 'Select',
                'value' => ''
            ]
        ];
        $data = \Nubersoft\nApp::call('nQuery')->query("SELECT menuName as `name`, menuVal as `value` FROM dropdown_menus WHERE assoc_column = ? AND page_live = 'on'", [$assoc_column])->getResults();
        if (empty($data))
            return $def;

        return array_map(function ($v) use ($selected) {
            if ($selected == $v['value'])
                $v['selected'] = true;
            return $v;
        }, array_merge($data, $def));
    }
}
