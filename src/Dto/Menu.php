<?php
namespace Nubersoft\Dto;

class Menu extends \SmartDto\Dto
{
    public $ID = 0;
    public $unique_id = '';
    public $parent_id = '';
    public $full_path = '';
    public $menu_name = '';
    public $group_id = '';
    public $page_options = '';
    public $link = '';
    public $template = '';
    public $use_page = '';
    public $auto_cache = '';
    public $in_menubar = '';
    public $is_admin = 0;
    public $page_type = '';
    public $auto_fwd = '';
    public $auto_fwd_post = '';
    public $session_status = 'off';
    public $usergroup = 0;
    public $page_live = 'off';
    public $page_order = 0;	
}