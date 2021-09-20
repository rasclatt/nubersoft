<?php
namespace Nubersoft\Dto\DataNode\Routing;

class GetRequest extends \SmartDto\Dto
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
    public $in_menubar = '';
    public $is_admin = 0;
    public $page_type = '';
    public $auto_fwd = '';
    public $auto_fwd_post = '';
    public $session_status = '';
    public $usergroup = '';
    public $page_live = '';
    public $page_order = 0;
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return \Nubersoft\nApp::call()->getDataNode('routing');
    }
}