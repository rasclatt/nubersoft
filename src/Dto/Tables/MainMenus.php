<?php
namespace Nubersoft\Dto\Tables;

class MainMenus extends \Nubersoft\Dto\Tables
{
    public string $parent_id = '';
    public string $full_path = '';
    public string $menu_name = '';
    public string $group_id = '';
    public string $page_options = '';
    public string $link = '';
    public string $template = '';
    public string $use_page = '';
    public string $auto_cache = '';
    public string $in_menubar = '';
    public int $is_admin = 0;
    public string $page_type = 'page';
    public string $auto_fwd = '';
    public string $auto_fwd_post = '';
    public string $session_status = 'off';
    public string $usergroup = '';
    public string $page_live = 'off';
    public int $page_order = 0;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        $array = parent::{__FUNCTION__}($array);
        $array['table'] = 'main_menus';
        return $array;
    }
}