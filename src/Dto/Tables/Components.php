<?php
namespace Nubersoft\Dto\Tables;

class Components extends \Nubersoft\Dto\Tables
{
    public int $page_order = 0;
    public string $ref_page = '';
    public string $parent_id = '';
    public string $ref_anchor = '';
    public string $title = '';
    public string $category_id = '';
    public string $component_type = '';
    public string $content = '';
    public string $file = '';
    public string $file_size = '';
    public string $file_path = '';
    public string $file_name = '';
    public string $timestamp = '';
    public string $admin_notes = '';
    public string $usergroup = '';
    public string $group_id = '';
    public string $cached = '';
    public string $page_live = 'off';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'component';
        return $array;
    }
}