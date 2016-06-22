<?php
	
	class	MenuButton
		{
			public		$thislayout;
			
			protected	$sub_button;
			protected	$_id;
			protected	$nuber;
			protected	$nubsql;
			protected	$nubquery;
			protected	$component;
			
			public	function __construct()
				{
					register_use(__METHOD__);
					
					AutoloadFunction('nQuery,get_edit_status');
					$this->component	=	new ComponentEditor();
					$_toggle			=	(get_edit_status())?	array(): array("page_live"=>'on');// && $_SESSION['toggle']['edit'] <= 1
					if(!isset(NubeData::$settings->gmenu) && nApp::siteValid()) {
						
						NubeData::$settings->gmenu	=	organize(nQuery()	->select()
																			->from("main_menus")
																			->where(array_merge(array("in_menubar"=>'on'),$_toggle))
																			->orderBy(array("page_order"=>"ASC"))->fetch(),'unique_id');
						
						NubeData::$settings->gsub	=	organize(nQuery()	->select()
																			->from("menu_display")
																			->fetch(),'parent_id');
					}
				}
			
			public	function Fetch($button_name = false,$retun_type = 1,$textcase = 0)
				{	
					register_use(__METHOD__);
					
					if(nApp::siteValid()) {
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
							$menu		=	nQuery()	->select()
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

			public	function UseLayout($settings = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('set_default,check_empty');
					$layout				=	(!empty($settings['layout']))? $settings['layout']:false;
					$_bypass			=	(!empty($settings['bypass']))? $settings['bypass']:false;
					$allowBypass		=	($_bypass != false && is_file($_file = ROOT_DIR.$_bypass));
					$menus				=	(!empty($settings['button_name']))? $settings['button_name']:false;
					$_menu_on			=	(NubeData::$settings->gmenu != false)? NubeData::$settings->gmenu:false;
					$valid				=	is_file($usefile = NubeData::$settings->site->template_folder.'menu.php');
					
					if(!$valid) {
							$usefile	=	TEMPLATE_DIR.'/default/menu.php';
						}
					
					ob_start();
					$includer			=	($allowBypass)? $_file : set_default($layout,$usefile);
					include($includer);
					$this->thislayout	=	ob_get_contents();
					ob_end_clean();
					
					return $this;
				}
			
			public	function GraphicMenu($_bypass = false,$retun_type = false,$textcase = 'ucwords')
				{
					register_use(__METHOD__);
					
					$settings['bypass']			=	$_bypass;
					$settings['button_name']	=	(isset($this->sub_button))? $this->sub_button:false;
					$allowBypass				=	($_bypass != false && is_file($_file = ROOT_DIR.$_bypass))? true:false;

					if($allowBypass	== false) {
						if(!isset($this->thislayout))
							$this->UseLayout($settings);
					}
						
					return $this;
				}

			public	function FetchSub()
				{
					$statementAdmin	=	(get_edit_status() && is_admin())?	array(): array("page_live"=>'on');
					$sub			=	nQuery()	->select()
													->from("menu_display")
													->where(array_merge($statementAdmin,array("parent_id"=>nApp::getPage()->unique_id)))
													->fetch();
					if($sub !== 0) {
						foreach($sub as $result) {
							$_button[]	=	$result;
						}
					}
					
					$this->sub_button	=	(isset($_button))? $_button:false;
					
					return $this;
				}

			public	function Component($_payload,$_menunique_id = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('FetchUniqueId');
					$_live			=	(!empty($_payload->page_live) && $_payload->page_live == 'on');
					$_unique		=	(!empty($_payload->unique_id));
					$parent_id		=	($_menunique_id != false)? $_menunique_id: "";
					$this->_id		=	(!empty($_payload->unique_id))? $_payload->unique_id: FetchUniqueId();
					$addComponent	=	($_unique)? $_payload->unique_id: 'ADD COMPONENT';
					$_locked		=	(!empty($_payload->admin_lock) && $_payload->admin_lock = 'on');
					$_file			=	(!empty($_payload->file_path));
					$content		=	(!empty($_payload->content))?	$_payload->content: 'Blank Content';
					$stripText		=	str_replace(array("&amp;", "&lt;", "amp;", "lt;", "quot;", "/pgt;", "pgt;"), "" , $content);
					
					include(RENDER_LIB.'/class.html/MenuButton/Component.php');
				}
		}