<?php
namespace Nubersoft\Dto\Tables;

class DropdownMenus extends \Nubersoft\Dto\Tables
{
    public string $assoc_column = '';
    public string $menuName = '';
    public string $menuVal = '';
    public string $page_order = '';
    public string $restriction = '';
    public string $page_live = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'dropdown_menus';
        return $array;
    }
}