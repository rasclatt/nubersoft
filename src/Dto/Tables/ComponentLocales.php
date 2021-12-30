<?php
namespace Nubersoft\Dto\Tables;

class ComponentLocales extends \Nubersoft\Dto\Tables
{
    public string $comp_id = '';
    public string $locale_abbr = '';
    public string $page_live = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'component_locales';
        return $array;
    }
}