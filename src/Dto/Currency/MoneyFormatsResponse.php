<?php
namespace Nubersoft\Dto\Currency;

class MoneyFormatsResponse extends \SmartDto\Dto
{
    public $lang = '';
    public $abbr2 = '';
    public $abbr3 = '';
    public $available = [];
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array['available'] = ($array['all_lang'])?? [];
        return $array;
    }
}