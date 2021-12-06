<?php
namespace Nubersoft\Dto\Helper\Request;

class GetRequest extends \SmartDto\Dto
{
    public $type = 'GET';
    public $to_object = false;
    public $key = null;
    /**
     *	@description	
     *	@param	
     */
    protected function type(): void
    {
        $this->type = strtolower($this->type);
    }
}