<?php
namespace Nubersoft;

class Plugin extends nRender
{
    use nMarkup\enMasse;
    use System\enMasse;
    use Conversion\enMasse;
    use nDynamics;
        
    protected    static    $plugin_content    =    [];
    protected    static    $current_plugin    =    [];
    
    public    function getPlugin($name, $file = false, $path = false)
    {
        if(empty($file))
            $file    =    'index.php';
        
        $file    =    $this->pluginExists($name, $file);
        
        ob_start();
        
        
        if(($this->getSystemOption('fileid') == 'on')) {
            $dev    =    ($this->getSystemOption('devmode') == 'dev');
            if(!$this->isAjaxRequest() && $this->getPage('is_admin') != 1 && $dev)
                echo '<div class="file-id-backtrace">'.$file.'</div>';
        }
        
        try {
            # Get the path for the plugin
            $this->renderer($file, $path);
        }
        catch(HttpException $e) {
            $data    =    $e->getMessage();
        }
        
        if(!isset($data))
            $data    =    ob_get_contents();

        ob_end_clean();
        
        return $data;
    }
    /**
     *    @description    Used to wrap the include so that $data does not become a reserved word
     */
    protected    function renderer($plugin, $path)
    {
        if(!empty($plugin)) {
            # If path exists
            if($path)
                echo $plugin;
            else
                include($plugin);
        }
    }
    
    public    function pluginExists($name, $file = false)
    {
        if(empty($file))
            $file    =    'index.php';
        
        $exists    =    false;
        
        foreach($this->getPluginPaths() as $val) {
            if(empty($val))
                continue;

            $plugin = $this->toSingleDs($val.DS.$name.DS.$file);

            if(is_file($plugin)) {
                if(empty(self::$current_plugin[$name])){
                    self::$current_plugin[$name]    =    [
                        'name' => $name,
                        'file' => $file, 
                        'path' => $plugin,
                        'dir' => pathinfo($plugin, PATHINFO_DIRNAME),
                        'root' => str_replace(NBR_ROOT_DIR, '', $plugin)
                    ];
                }

                return $plugin;
            }
        }
        return false;
    }
    
    
    private    function getPluginPaths()
    {
        $paths    =    (!empty($this->getDataNode('plugins')['paths']))? $this->getDataNode('plugins')['paths'] : [];
        
        return $paths;
    }
    
    public    function getPluginInfo($name = false)
    {
        if($name)
            return (!empty(self::$current_plugin[$name]))? self::$current_plugin[$name] : false;
        
        return self::$current_plugin;
    }
    
    public    function setPluginContent($name, $value)
    {
        self::$plugin_content[$name]    =    $value;
        return $this;
    }
    
    public    function getPluginContent($name = false, $clear = true)
    {
        if($name) {
            $data = (!empty(self::$plugin_content[$name]))? self::$plugin_content[$name] : false;
            if(!empty($data)) {
                if($clear)
                    unset(self::$plugin_content[$name]);
                
                return $data;
            }

            return false;
        }
        
        $data    =    self::$plugin_content;
        
        if($clear)
            self::$plugin_content    =    null;
        
        return $data;
    }
    
    public    function getShortCode($decode = false)
    {
        $data    =    $this->getDataNode('current_matched_plugin_content');
        
        if(!empty($data[1]))
            return ($decode)? json_decode($data[1], 1) : $data[1];
        
        return false;
    }
}