<?php
namespace nPlugins\Nubersoft;

class	MenuButton extends \nPlugins\Nubersoft\CoreHelper
	{
		public		$thislayout;
		
		protected	$sub_button,
					$_id,
					$component;
		
		public	function __construct(\nPlugins\Nubersoft\core $core)
			{
				$this->component	=	new ComponentEditor();
				$this->getSubMenus();
				$this->getPageLiveMenus($core);
				return parent::__construct();
			}
		
		public	function getPageLiveMenus(\nPlugins\Nubersoft\core $core)
			{
				$_toggle	=	$core->getEditStatus();
				if(empty($this->getDataNode('gmenu')) && $this->siteValid()) {
					foreach($this->getDataNode('menu_data') as $unique_id => $row) {
						if($row->page_live != 'on') {
							if(!$_toggle)
								continue;
						}
						
						$row->page_options	=	json_decode($this->safe()->decode($row->page_options));
						$gmenu[$unique_id]	=	$row;
					}
					
					if(isset($gmenu))
						$this->saveSetting('gmenu',$gmenu);
				}
				
				return (isset($gmenu))? $gmenu : false;
			}
		
		public	function getSubMenus()
			{
				if(!empty($this->getDataNode('gsub')))//gmenu
					return $this->getDataNode('gsub');//gmenu
					
				$gsub	=	$this->organizeByKey($this->nQuery()
					->select()
					->from("components")
					->where(array('ref_spot'=>'sub_menu'))
					->fetch(),'ref_page',array('unset'=>false));
				
				$this->saveSetting('gsub',$gsub);
				
				return $gsub;
			}
		
		public	function fetch($button_name = false,$retun_type = 1,$textcase = 0)
			{	
				if($this->siteValid()) {
					if($button_name !== false) {
						if(!is_array($button_name))  
							$sql[]	=	"menu_name = '".$button_name."'";
						else {
							foreach($button_name as $action => $name) {
								if(preg_match('/login/i',$action) && !is_numeric($action)) {
									if(isset($_SESSION['username']))
										$menus[]	=	"menu_name = '".$name."'";
								}
								else
									$menus[]	=	"menu_name = '".$name."'";
							}
							
							if(!empty($menus))	
								$sql[]	=	implode(" or ",$menus);
							
							$_override	=	true;
						}
					}
					
					if(!isset($sql) && !isset($_override)) {
						$sql[]	=	"in_menubar = 'on'";
						$sql[]	=	"page_live = 'on'";
					}
					
					if(isset($sql)) {
						$sql_string	=	(isset($sql) && !empty($sql))? ' where '.implode(" and ",$sql):'';
						$menu		=	$this->nQuery()
											->select()
											->from("main_menus")
											->addCustom($sql_string)
											->orderBy(array("page_order"=>"ASC"))
											->fetch();
						
						if($menu !== 0) {
							foreach($menu as $buttons) {
								if($retun_type !== 1)
									$menu_array['raw'][]	=	$buttons;
								if($textcase == 'ucwords')
									$words	=	ucwords($buttons['menu_name']);
								elseif($textcase == 'upper')
									$words	=	strtoupper($buttons['menu_name']);
								elseif($textcase == 'lower')
									$words	=	strtolower($buttons['menu_name']);
								else
									$words	=	$buttons['menu_name'];
								
								$menu_array['links'][]	=	'<a href="'.$buttons['full_path'].'" class="main_menu">'.$words.'</a>';
							}
						}
						
						if(!empty($menu_array)) {
							if($retun_type == 1)
								echo (isset($menu_array['links']))? implode("",$menu_array['links']):'';
							else
								return $menu_array;
						}
					}
				}
			}

		public	function useLayout($settings = false)
			{
				$layout				=	(!empty($settings['layout']))? $settings['layout']:false;
				$_bypass			=	(!empty($settings['bypass']))? $settings['bypass']:false;
				$allowBypass		=	($_bypass != false && is_file($_file = NBR_ROOT_DIR.$_bypass));
				$menus				=	(!empty($settings['button_name']))? $settings['button_name']:false;
				$_menu_on			=	$this->getDataNode('gmenu');
				$valid				=	is_file($usefile = $this->getSite()->template.DS.'frontend'.DS.'menu.php');
				
				if(!$valid) {
					$usefile	=	NBR_TEMPLATE_DIR.DS.'default'.DS.'frontend'.DS.'menu.php';
				}
				
				$set_default	=	function($setto = false, $setfrom = false)
					{
						return (empty($setto))? $setfrom : $setto;
					};
					
				ob_start();
				$includer			=	($allowBypass)? $_file : $set_default($layout,$usefile);
				include($includer);
				$this->thislayout	=	ob_get_contents();
				ob_end_clean();
				
				return $this;
			}
		
		public	function graphicMenu($_bypass = false,$retun_type = false,$textcase = 'ucwords')
			{
				$settings['bypass']			=	$_bypass;
				$settings['button_name']	=	(isset($this->sub_button))? $this->sub_button:false;
				$allowBypass				=	($_bypass != false && is_file($_file = NBR_ROOT_DIR.$_bypass))? true:false;

				if($allowBypass	== false) {
					if(!isset($this->thislayout))
						$this->useLayout($settings);
				}
					
				return $this;
			}

		public	function fetchSub()
			{
				$statementAdmin	=	($this->getPlugin('\nPlugins\Nubersoft\core')->getEditStatus() && $this->isAdmin())? array(): array("page_live"=>'on');
				$sub	=	$this->nQuery()
								->select()
								->from("components")
								->where(array_merge($statementAdmin,array(
									'ref_spot'=>'sub_menu',
									"parent_id"=>$this->getPageURI('unique_id')
								)))
								->fetch();
	
				if($sub !== 0) {
					foreach($sub as $result) {
						$_button[]	=	$result;
					}
				}
				
				$this->sub_button	=	(isset($_button))? $_button:false;
				
				return $this;
			}

		public	function component($_payload = false,$_menunique_id = false)
			{
				$_live			=	(!empty($_payload->page_live) && $_payload->page_live == 'on');
				$_unique		=	(!empty($_payload->unique_id));
				$parent_id		=	($_menunique_id != false)? $_menunique_id: "";
				$this->_id		=	(!empty($_payload->unique_id))? $_payload->unique_id: $this->fetchUniqueId();
				$addComponent	=	($_unique)? $_payload->unique_id : 'ADD COMPONENT';
				$_locked		=	(!empty($_payload->admin_lock) && $_payload->admin_lock = 'on');
				$_file			=	(!empty($_payload->file_path));
				$content		=	(!empty($_payload->content))?	$_payload->content: 'Blank Content';
				//$stripText		=	str_replace(array("&amp;", "&lt;", "amp;", "lt;", "quot;", "/pgt;", "pgt;"), "" , $content);
				$stripText		=	$content;
				
				include(__DIR__.DS.'MenuButton'.DS.'Component.php');
			}
	}