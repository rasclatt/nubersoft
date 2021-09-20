<?php
namespace Nubersoft\Dto\DataNode\Header;

class GetRequest extends \SmartDto\Dto
{
    public $header_response_code = 200;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return \Nubersoft\nApp::call()->getDataNode('header');
    }
}