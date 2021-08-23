<?php
namespace Nubersoft\Dto\Settings\Page\View;

use Nubersoft\ {
    DataNode,
    Html,
    nUser
};

class ConstructRequest extends \SmartDto\Dto
{
    public $DataNode;
    public $Html;
    public $nUser;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['DataNode'] = new DataNode();
        $array['Html'] = new Html();
        $array['nUser'] = new nUser();

        return $array;
    }
}