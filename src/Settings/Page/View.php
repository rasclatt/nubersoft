<?php
namespace Nubersoft\Settings\Page;

use \Nubersoft\ {
    nApp,
    nQuery,
    Plugin
};

use \Nubersoft\Dto\Settings\Page\View\ConstructRequest as Helpers;

/**
 * @description 
 */
class View extends Controller
{
    private $Plugin;
    protected $user = false;
    /**
     * @description 
     */
    public function __construct(Helpers $request)
    {
        $this->Plugin = new Plugin($request);
        parent::__construct();
    }
    /**
     * @description 
     */
    public function create($page, string $type = 'layout'): ?string
    {
        $data = $this->getPageComponents($page, false);
        $arr = $this->getContentStructure($page);

        if (empty($arr))
            return null;

        if (empty($this->user))
            $this->user = (new \Nubersoft\System\User(new nApp, new nQuery))->getUser();

        ob_start();
        $this->recurseRender($arr, $data, $type);
        $layout = ob_get_contents();
        ob_end_clean();

        return $layout;
    }
    /**
     * @description 
     */
    protected function recurseRender($array, $data, $type)
    {
        $editor = $this->getSession('editor');
        $usergroup = (!empty($this->user['usergroup'])) ? $this->user['usergroup'] : false;

        if (!empty($usergroup))
            $usergroup = (!is_numeric($this->user['usergroup'])) ? constant($this->user['usergroup']) : $usergroup;

        foreach ($array as $key => $value) {
            # Get the name of the component
            $compType = $data[$key]['component_type'];
            if (!$this->Plugin->getPlugin($type, $compType . '.php', true))
                $compType = 'code';

            # If the item is supposed to be a container
            $wrap = (in_array($compType, ['row', 'div', 'container']));
            # Set the name of the component for plugin data
            $comb = $type . '_' . $compType;
            # Set the id
            $ID = $data[$key]['ID'];
            # If no usergroup set or the current usergroup is allowed
            if (empty($data[$key]['usergroup']))
                $allowed = true;
            else {
                if (empty($usergroup))
                    $allowed = false;
                else
                    $allowed = ($usergroup <= $data[$key]['usergroup']);
            }
            # Stop conditions
            if ($type == 'layout' && $data[$key]['page_live'] != 'on') {
                continue;
            } elseif (!$allowed)
                continue;
            # Set data to key
            $itemData = $data[$key];
            # Create a wrapper if container
            echo ($wrap || ($type == 'editor')) ? PHP_EOL . '<div id="comp-' . $ID . '" class="component-parent item-container-' . $type . '-' . $data[$key]['component_type'] . ' container-' . ((!empty($itemData['page_live'])) ? $itemData['page_live'] : 'off') . '" data-itemid="' . $ID . '">' . PHP_EOL : '';
            # Render the component plugin
            echo $this->Plugin->setPluginContent($comb, $itemData)
                ->getPlugin($type, $compType . '.php');

            if (!empty($value)) {
                echo '<div class="container-' . (($editor) ? '' : 'view-') . 'gapped wrapper-' . $data[$key]['page_live'] . '">';
                $this->recurseRender($value, $data, $type);
                echo '</div>';
            }

            echo ($wrap || ($type == 'editor')) ? '</div>' . PHP_EOL : '';
        }
    }
}
