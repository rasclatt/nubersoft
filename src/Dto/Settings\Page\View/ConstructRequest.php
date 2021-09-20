<?php
namespace Nubersoft\Dto\Settings\Page\View;

use Nubersoft\ {
    DataNode,
    Html,
    nUser,
    Settings\Controller as Settings,
    nMarkUp as MarkDown,
    nRouter as Router,
    nCookie as Cookie
};

class ConstructRequest extends \SmartDto\Dto
{
    public $DataNode, $Html, $nUser, $Settings, $MarkDown, $Router, $Cookie;

    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['DataNode'] = new DataNode();
        $array['Html'] = new Html();
        $array['nUser'] = new nUser();
        $array['Settings'] = new Settings();
        $array['MarkDown'] = new MarkDown();
        $array['Router'] = new Router();
        $array['Cookie'] = new Cookie();

        return $array;
    }
}