<?php

	class nGet
		{
			private	static	$singleton;
			
			public	function __construct()
				{
					if(empty(self::$singleton))
						self::$singleton	=	$this;
					
					return self::$singleton;
				}
			// This will retrieve core elements from the NubeData
			public	function getCoreElement($type = false)
				{
					if(empty($type)) {
						return (!empty(NubeData::$settings))? NubeData::$settings : false;
					}

					switch($type) {
							case ('pref'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('menu'):
								if(isset(NubeData::$settings->menu_data)) {
									$menu['menu_data']		=	(isset(NubeData::$settings->menu_data))? NubeData::$settings->menu_data : false;
									$menu['menu_struc']		=	(isset(NubeData::$settings->menu_struc))? NubeData::$settings->menu_struc : false;
									$menu['menu_dir']		=	(isset(NubeData::$settings->menu_dir))? NubeData::$settings->menu_dir : false;
									$menu['menu_hiearchy']	=	(isset(NubeData::$settings->menu_hiearchy))? NubeData::$settings->menu_hiearchy : false;
									$menu['menu_current']	=	(isset(NubeData::$settings->menu_current))? NubeData::$settings->menu_current : false;

									return Safe::to_object($menu);
								}
							case ('page'):
								if(isset(NubeData::$settings->page_prefs))
									return NubeData::$settings->page_prefs;
							case ('site'):
								if(isset(NubeData::$settings->site))
									return NubeData::$settings->site;
							case ('user'):
								if(isset(NubeData::$settings->user))
									return NubeData::$settings->user;
							case ('connection'):
								if(isset(NubeData::$settings->connection))
									return NubeData::$settings->connection;
							case ('head'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('engine'):
								if(isset(NubeData::$settings->engine))
									return NubeData::$settings->engine;
							case ('plugin'):
								if(isset(NubeData::$settings->plugin))
									return NubeData::$settings->plugin;
							case ('prefs'):
								if(isset(NubeData::$settings->preferences))
									return NubeData::$settings->preferences;
							case ('bypass'):
								if(isset(NubeData::$settings->bypass))
									return NubeData::$settings->bypass;
						}

					return false;
				}
			// This will get the site prefs from NubeData
			public	function getSitePrefs($refresh = false)
				{
					$prefs	=	$this->getCoreElement('pref');
					
					if(!empty($prefs->site) && !$refresh)
						return $prefs->site;
					else {
						$make	=	$this->getSetSitePrefs(true);
						$prefs	=	$this->getCoreElement('pref');
						return (!empty($prefs->site))? $prefs->site : false;
					}
					
				}
			//  This will fetch the company logo from the NubeData
			public	function getSiteLogo()
				{
					$prefs	=	$this->getSitePrefs();
					if(!empty($prefs) && !empty($prefs->content->companylogo))
						return $prefs->content->companylogo;
						
					return false;
				}
			
			public	function getMenu($type = 'menu_current')
				{
					if(!empty($this->getCoreElement('menu')->{$type}))
						return $this->getCoreElement('menu')->{$type};
				}
			
			public	function getIncidentals()
				{
					if(!empty(NubeData::$incidentals))
						return NubeData::$incidentals;
				}
				
			public	function getErrors()
				{
					if(!empty(NubeData::$errors))
						return NubeData::$errors;
				}
			
			public	function getPage($var = false)
				{
					if(empty(CoreMySQL::$CoreAttributes))
						CoreMySQL::Initialize(true);
					
					if(CoreMySQL::$CoreAttributes) {
						$array	=	Safe::to_object(CoreMySQL::$CoreAttributes);
						if(!empty($array)) {
							if(!empty($var))
								return (isset($array->{$var}))? $array->{$var} : false;
							else
								return $array;
						}
					}
					
					return false;
				}
				
			public	function getSite($var = false)
				{
					$val	=	$this->getCoreElement('site');
					if(!empty($val)) {
						if(!empty($var))
							return (isset($val->{$var}))? $val->{$var} : false;
						else
							return $val;
					}
				}
			
			public	function getSetSitePrefs($refresh = false)
				{
					if(!$refresh) {
						$head	=	$this->getCoreElement('head');
						if(!empty($head)) {
							$prefs	=	Safe::to_array($this->getCoreElement('head'));
							if($return != false)
								return (isset($prefs[$return]))? Safe::to_object($prefs[$return]) : false;
							else
								return (is_array($prefs))? Safe::to_object($prefs) : false;
						}
					}
					 
					AutoloadFunction('nQuery,organize,create_default_prefs');
					$nubquery	=	nQuery();
					if(!$nubquery) {
						RegistryEngine::saveSetting('critical',array('nubquery_engine'=>false));
						RegistryEngine::saveError('database',array('nubquery_engine'=>false));	
						return false;
					}

					$exists		=	$nubquery	->select("COUNT(*) as count")
												->from("system_settings")
												->where(array("name"=>"settings"))
												->fetch();
		
					// Create the prefs
					if($exists[0]['count'] == 0 || !isset($exists[0]['count'])) {
						create_default_prefs();
					}
						
					$vals		=	organize($nubquery	->select()
														->from("system_settings")
														->where(array("name"=>"settings"))
														->fetch(),
														'page_element');
					
					if(empty($vals)) {
						RegistryEngine::saveError("invalid_table","system_settings");
						return false;
					}
					
					foreach($vals as $name => $settings) {
						$vals[$name]['content']	=	json_decode($vals[$name]['content']);
					}
						
					$prefs['site']		=	(isset($vals['settings_site']))? $vals['settings_site']:false;
					$prefs['header']	=	(isset($vals['settings_head']))? $vals['settings_head']:false;
					$prefs['footer']	=	(isset($vals['settings_foot']))? $vals['settings_foot']:false;
					
					if(in_array(false,$prefs)) {
						$key	=	array_keys($prefs,false);
						
						if(!empty($key)) {
							foreach($key as $try) {
								create_default_prefs($try);
							}
						}
					}
					
					RegistryEngine::saveSetting('preferences',$prefs);	
					
					return Safe::to_object($prefs);
				}
			
						public	function getColumns($table)
				{
					if(!nQuery())
						return 0;
					
					$getDesc	=	nQuery()->describe($table)->fetch();
					$cols		=	organize($getDesc,"Field");
					
					if(!is_array($cols))
						return 0;
					else
						$cols	=	array_keys($cols);
					// Auto-save attributes
					$this->getColumnInfo($table, $getDesc);
					// Save the columns only
					nApp::saveSetting('columns_in_'.$table, $cols);
					
					return $cols;
				}
			
			public	function getColumnInfo($table = false,$cols = false)
				{
					if(isset(NubeData::$settings->{"col_attr_in_".$table}))
						return NubeData::$settings->{"col_attr_in_".$table};
					
					if(!is_array($cols)) {
						if($table)
							nApp::saveSetting('col_attr_in_'.$table, $cols);
					}
					else {
						$new	=	array();
						foreach($cols as $colname) {
							$null	=	(isset($colname['Null']) && strtolower($colname['Null']) == 'yes');
							$extra	=	(!empty($colname['Extra']) && strtolower($colname['Extra']) == 'auto_increment');
							$type	=	(strpos(strtolower($colname['Type']),'int') !== false);
							
							$new[$colname['Field']]['allow_null']	=	$null;
							$new[$colname['Field']]['auto_inc']		=	$extra;
							$new[$colname['Field']]['is_int']		=	$type;
						}
						
						nApp::saveSetting('col_attr_in_'.$table, $new);
					}
				}
			
			public	function getRoutingTables()
				{
					if(!nQuery())
						return 0;
					
					$tables	=	nQuery()->query("select table_name,table_id from `routing_table`")->getResults();
					nApp::saveSetting('routing_tables',$tables);
					
					return $tables;
				}
			
			public	function getPageURI()
				{
					// Set Directory and Query string
					if(isset($_SERVER['SCRIPT_URL']))
						$query_uri	=	$_SERVER['SCRIPT_URL'];
					elseif(isset($_SERVER['REDIRECT_URL']))
						$query_uri	=	$_SERVER['REDIRECT_URL'];
					else
						$query_uri	=	DS;
					
					$uri['subdir']	=	str_replace(DS.DS,DS,str_replace(DS.DS,DS,preg_replace("/[^0-9a-zA-Z\_\-\/]/","",$query_uri)));
					$uri['query']	=	str_replace(DS.DS,DS,DS.preg_replace("/([^\?]{1,})\?([^\?]{1,})/","$2",$_SERVER['REQUEST_URI'])."/");
					$uri['query']	=	($uri['subdir'] == $uri['query'])? false : trim($uri['query'],"/");
					// If only the forward slash is remaining, then that indicates home page
					// Because of the way the page builder rebuilds the paths, the home will fail
					$homefind		=	($uri['subdir'] == DS)? array("is_admin"=>2) : array("full_path"=>$uri['subdir']);
					// Fetch the path from the database
					$base			=	nQuery()	->select()
													->from("main_menus")
													->where($homefind)
													->fetch();
													
					// If path is found			
					$result			=	($base != 0)? $base[0] : false;
					
					nApp::saveSetting('pageURI',$result);
					
					return $result;
				}
			
			public	function getAdminPage()
				{
					$nQuery	=	nQuery();
					
					if(!empty($nQuery)) {
						$admin	=	$nQuery	->select(array("menu_name","link","full_path"))
											->from("main_menus")
											->where(array("is_admin"=>1))
											->fetch();
					}
					else
						$admin = 0;
						
					$setting	=	($admin != 0)? $admin[0] : false;
					
					nApp::saveSetting('admin_page',$setting);
					
					return $setting;
				}
			
			public	function getDropDowns($table = false)
				{
					$cols	=	Safe::to_array(nApp::getColumns($table));
					
					if(empty($cols))
						return false;
						
					$query	=	nQuery();
					$dDowns	=	$query	->select(array("assoc_column","menuName","menuVal"))
										->from('dropdown_menus')
										->whereIn("assoc_column",$cols)
										->fetch();
										
					if(!empty($dDowns)) {
						foreach($dDowns as $rows) {
							$new[$rows['assoc_column']][]	=	array("value"=>$rows['menuVal'],"name"=>ucfirst($rows['menuName']));
						}
					}

					$new	=	(!empty($new))? $new : false;
					
					nApp::saveSetting('dropdowns_'.$table,$new);
					
					return $new;
				}
			
			public	function getFormBuilder()
				{
					$cols = organize(nQuery()	->select(array("column_name","column_type","size"))
												->from("form_builder")
												->where(array("page_live"=>'on'))
												->fetch(), 'column_name',true);
					
					nApp::saveSetting('form_builder',$cols);
					
					return $cols;
				}
			
			public	function getAllMenus()
				{
					$menus	=	nQuery()	->select(array("ID","unique_id","parent_id","link","menu_name","is_admin","in_menubar","full_path","page_options","page_live"))
											->from("main_menus")
											->orderBy(array("page_order"=>"ASC"))
											->fetch();
					
					nApp::saveSetting('all_menus',$menus);
					
					return $menus;
				}
			
			public	function getUserInfo($username = false)
				{
					$username	=	trim($username);
					if(empty($username))
						return false;
					
					$user	=	nQuery()	->select()
											->from('users')
											->where(array('username'=>$username))
											->fetch();
					
					return ($user == 0)? false : $user;
				}
			
			public	function getUserCount()
				{
					$count	=	nQuery()	->select('COUNT(*) as count')
											->from('users')
											->fetch();
											
					return $count[0]['count'];
				}
			
			public	function getFileTypes()
				{
					$types	=	nQuery()	->select(array("file_extension"))
											->from('file_types')
											->where(array("page_live"=>'on'))
											->fetch();
					
					if($types == 0)
						return 0;
					
					return array_keys(\nApp::nFunc()->organizeByKey($types,'file_extension'));
				}
		}