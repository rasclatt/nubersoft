<?php
namespace Nubersoft\Dto\Helper\Html;

class CreateScriptRequest extends \SmartDto\Dto
{
    public bool $is_local = false;
    public string $src;
    public string $type = 'text/javascript';
    public string $id = '';
    public string $attr = '';
    /**
     *	@description	
     *	@param	
     */
    protected function src()
    {
        if($this->is_local)
            $this->src .= '?v=' . filemtime(str_replace(DS . DS, DS, NBR_DOMAIN_ROOT . DS . str_replace('/', DS, $this->src)));
    }
    /**
     *	@description	
     *	@param	
     */
    protected function id()
    {
        if(!empty($this->id))
            $this->id = ' id="' . $this->id . '"';
    }
}