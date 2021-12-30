<?php
namespace Nubersoft\Dto\Tables;

class FormBuilder extends \Nubersoft\Dto\Tables
{
    public string $column_type = '';
    public string $column_name = '';
    public string $size = '';
    public string $default_setting = '';
    public int $restriction = 0;
    public int $page_order = 0;
    public string $page_live = 'off';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'form_builder';
        return $array;
    }
}