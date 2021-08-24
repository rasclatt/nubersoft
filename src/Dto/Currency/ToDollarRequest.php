<?php
namespace Nubersoft\Dto\Currency;

class ToDollarRequest extends ToMoneyRequest
{
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $split = (!empty($array['format']->lang))? explode('_', $array['format']->lang) : ['en', 'US'];
        $array = array_merge($array, array_combine(['lang', 'country'], $split));
        return parent::beforeConstruct($array);
    }
}