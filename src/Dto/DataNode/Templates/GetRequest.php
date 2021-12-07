<?php
namespace Nubersoft\Dto\DataNode\Templates;

use \Nubersoft\nApp;

class GetRequest extends \SmartDto\Dto
{
    public $paths = [];
    public $config = [];
    public $errors = '';
    public $frontend = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return nApp::call()->getDataNode('templates');
    }
}