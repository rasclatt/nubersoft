<?php
namespace Nubersoft\Dto\Router;

class GetPageResponse extends \SmartDto\Dto
{
    public $ID = 0;
    public $unique_id = 0;
    public $parent_id = '';
    public $full_path = '';
    public $menu_name = '';
    public $group_id = '';
    public $page_options = '';
    public $link = '';
    public $template = '';
    public $use_page = '';
    public $auto_cache = '';
    public $in_menubar = false;
    public $is_admin = false;
    public $page_type = '';
    public $auto_fwd = '';
    public $auto_fwd_post = '';
    public $session_status = '';
    public $usergroup = 3;
    public $page_live = 'off';
    public $page_order = 1;
    public $is_valid = false;

    /**
     *	@description	
     *	@param	
     */
    protected function is_valid()
    {
        $this->is_valid = $this->ID > 0;
    }
}