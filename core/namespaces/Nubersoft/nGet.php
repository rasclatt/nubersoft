<?php
namespace Nubersoft;
/*
**	This class is a query and fetch class that is mainly used by the nApp to get results,
**	though it can be used by itself
*/
class nGet extends \Nubersoft\nFunctions
	{
		protected	$nApp;
		
		public	function __construct()
			{
				$this->nApp	=	nApp::call();
				return parent::__construct();
			}
		/*
		**	@description	This method checks if the data node is set
		*/
		public	function issetCoreElement($type)
			{
				return (isset(NubeData::$settings->{$type}));
			}
		# This will retrieve core elements from the NubeData
		public	function getCoreElement($type = false)
			{
				if(empty($type)) {
					return (!empty(NubeData::$settings))? NubeData::$settings : false;
				}
				
				switch($type) {
					case ('menu'):
						if(!empty($this->nApp->getData()->getMenuData())) {
							$menu['menu_data']		=	$this->nApp->getData()->getMenuData();
							$menu['menu_struc']		=	$this->nApp->getData()->getMenuStruc();
							$menu['menu_dir']		=	$this->nApp->getData()->getMenuDir();
							$menu['menu_hiearchy']	=	$this->nApp->getData()->getMenuHiearchy();
							$menu['menu_current']	=	$this->nApp->getData()->getMenuCurrent();

							return $this->toObject($menu);
						}
					case ('page'):
						return $this->nApp->getData()->getPagePrefs();
					default:
						$isPrefs	=	array('head','prefs','pref');
						if(in_array($type,$isPrefs))
							return $this->nApp->getData()->getPreferences();
						elseif(isset(NubeData::$settings->{$type}))
							return NubeData::$settings->{$type};
				}

				return false;
			}
		# This will get the site prefs from NubeData
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
		#  This will fetch the company logo from the NubeData
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
					$this->getHelper('CoreMySQL')->initialize(true);
				
				if(CoreMySQL::$CoreAttributes) {
					$array	=	$this->toObject(CoreMySQL::$CoreAttributes);
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
						$prefs	=	$this->toArray($this->getCoreElement('head'));
						if($return != false)
							return (isset($prefs[$return]))? $this->toObject($prefs[$return]) : false;
						else
							return (is_array($prefs))? $this->toObject($prefs) : false;
					}
				}
				
				$nubquery	=	$this->nApp->nQuery();
				if(!$nubquery) {
					$this->nApp->saveSetting('critical',array('nubquery_engine'=>false));
					$this->nApp->saveError('database',array('nubquery_engine'=>false));	
					return false;
				}

				$exists		=	$nubquery	->select("COUNT(*) as count")
											->from("system_settings")
											->where(array("name"=>"settings"))
											->fetch();
				
				$query	=	$nubquery	->select()
										->from("system_settings")
										->where(array("name"=>"settings"))
										->fetch();
				
				$vals	=	$this->organizeByKey($query,'page_element');
				
				if(empty($vals)) {
					$this->nApp->saveError("invalid_table","system_settings");
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
							$this->nApp->getFunction('create_default_prefs',$try);
						}
					}
				}
				
				$this->nApp->saveSetting('preferences',$prefs);	
				
				return $this->toObject($prefs);
			}
		
		public	function getColumns($table)
			{
				if(!$this->nApp->nQuery())
					return 0;
				
				$getDesc	=	$this->nApp->nQuery()->describe($table)->getResults();
				$cols		=	$this->organizeByKey($getDesc,"Field");
				
				if(!is_array($cols))
					return 0;
				else
					$cols	=	array_keys($cols);
				# Auto-save attributes
				$this->getColumnInfo($table, $getDesc);
				# Save the columns only
				$this->nApp->saveSetting('columns_in_'.$table, $cols);
				
				return $cols;
			}
		
		public	function getColumnInfo($table = false,$cols = false)
			{
				$set	=	$this->issetCoreElement("col_attr_in_".$table);
				if($set)
					return $this->getCoreElement("col_attr_in_".$table);
				
				if(!is_array($cols)) {
					if($table)
						$this->nApp->saveSetting('col_attr_in_'.$table, $cols);
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
					
					$this->nApp->saveSetting('col_attr_in_'.$table, $new);
				}
			}
		
		public	function getRoutingTables()
			{
				if(!$this->nApp->nQuery())
					return 0;
				
				$tables	=	$this->nApp->nQuery()->query("select table_name,table_id from `routing_table`")->getResults();
				$this->nApp->saveSetting('routing_tables',$tables);
				
				return $tables;
			}
		
		public	function getScriptUri($def='/')
		{
			return (isset($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : '/';
		}
	
		public	function getPageURI($def = false,$reqUrl = false)
			{
				if(!empty($def))
					$query_uri	=	$def;
				# Set Directory and Query string
				elseif(isset($_SERVER['SCRIPT_URL']))
					$query_uri	=	rtrim(preg_replace('!^/index\.php!','',$_SERVER['SCRIPT_URL']),'/').'/';
				elseif(isset($_SERVER['REDIRECT_URL']))
					$query_uri	=	$_SERVER['REDIRECT_URL'];
				else
					$query_uri	=	(!empty($def))? $def : DS;

				$reqUrl			=	(!empty($reqUrl))? $reqUrl : $this->getScriptUri();
				
				$uri['subdir']	=	str_replace(DS.DS,DS,str_replace(DS.DS,DS,preg_replace("/[^0-9a-zA-Z\_\-\/]/","",$query_uri)));
				$uri['query']	=	str_replace(DS.DS,DS,DS.preg_replace("/([^\?]{1,})\?([^\?]{1,})/","$2",$reqUrl)."/");
				$uri['query']	=	($uri['subdir'] == $uri['query'])? false : trim($uri['query'],"/");
				$subDir			=	trim($uri['subdir'],'/');
				
				# If only the forward slash is remaining, then that indicates home page
				# Because of the way the page builder rebuilds the paths, the home will fail
				$homefind	=	($uri['subdir'] == DS)? array("is_admin"=>2) : array("full_path"=>$uri['subdir']);
	
				$nodeName	=	str_replace(',','',__FUNCTION__.implode('',$homefind));

				if(empty($this->nApp->getDataNode($nodeName))) {
					
					$nquery	=	$this->nApp->nQuery();
					# Fetch the path from the database
					$base	=	$nquery	->select()
									->from("main_menus")
									->where($homefind)
									->fetch();
					
					if(isset($base[0]['page_options']))
						$base[0]['page_options']	=	json_decode($this->safe()->decode($base[0]['page_options']));
					
					$this->nApp->saveSetting($nodeName,$base);
				}
				else
					$base	=	$this->toArray($this->nApp->getDataNode($nodeName));
				# If path is found			
				$result			=	($base != 0)? $base[0] : array('invalid_uri'=>$query_uri);
				# If there is a full path returned
				if(!empty($result['full_path'])) {
					# Try to extract whether the site is to use strict url matching
					$isStrict	=	$this->getMatchedArray(array('onload','strict','url'));
					# If the setting is available
					if(isset($isStrict['url'][0])) {
						# See what value is set
						$strictUrl	=	$this->getBoolVal($isStrict['url'][0]);
						if($strictUrl && !empty($homefind['full_path'])) {
							if($result['full_path'] != $homefind['full_path'])
								$result	=	false;
						}
					}
				}
				
				if(!empty($result['page_options']) && is_string($result['page_options'])) {
					$result['page_options']	=	json_decode($this->nApp->safe()->decode($result['page_options']));
				}
				# Save page settings to global
				$this->nApp->saveSetting('pageURI',$result);
				# Return the results
				return $result;
			}
		
		public	function getAdminPage()
			{
				$nquery	=	$this->nApp->nQuery();
				
				if(!empty($nquery)) {
					$cols	=	array(
									"menu_name",
									"link",
									"full_path"
								);
					
					$admin	=	$nquery	->select($cols)
										->from("main_menus")
										->where(array("is_admin"=>1))
										->fetch();
				}
				else
					$admin = 0;
					
				$setting	=	($admin != 0)? $admin[0] : false;
				
				$this->nApp->saveSetting('admin_page',$setting);
				
				return $setting;
			}
		
		public	function getDropDowns($table = false)
			{
				$cols	=	$this->toArray($this->nApp->getColumns($table));
				
				if(empty($cols))
					return false;
					
				$nquery	=	$this->nApp->nQuery();
				$dDowns	=	$nquery	->select(array("assoc_column","menuName","menuVal"))
									->from('dropdown_menus')
									->whereIn("assoc_column",$cols)
									->orderBy(array("page_order"=>'ASC'))
									->fetch();
									
				if(!empty($dDowns)) {
					foreach($dDowns as $rows) {
						$new[$rows['assoc_column']][]	=	array(
																"value"=>$rows['menuVal'],
																"name"=>ucfirst($rows['menuName'])
															);
					}
				}

				$new	=	(!empty($new))? $new : false;
				
				$this->nApp->saveSetting('dropdowns_'.$table,$new);
				
				return $new;
			}
		
		private	$allow;
		
		public	function allowSaved($allow = true)
			{
				$this->allow	=	$allow;
				return $this;
			}
		
		public	function getFormBuilder($columns = false,$gCols = array("column_name","column_type","size"),$org = 'column_name')
			{
				$OR		=	"WHERE";
				
				if(!empty($columns) && is_array($columns))
					$OR	.=	PHP_EOL."`column_name` = '".implode("' OR `column_name` = '",$columns)."' AND".PHP_EOL;
				
				$sql	=	"SELECT
								`".implode('`,`',$gCols)."`
							FROM
								`form_builder`
							{$OR}
								page_live = 'on'
							ORDER BY
								page_order ASC";
								
				$query	=	$this->nApp->nQuery()->query($sql)->getResults();	
				$cols	=	$this->organizeByKey($query, $org, true);
	
				if(is_bool($this->allow)) {
					if($this->allow !== true)
						return $cols;
				}
				
				$this->nApp->saveSetting('form_builder',$cols);
				
				return $cols;
			}
		
		public	function getAllMenus()
			{
				$nquery	=	$this->nApp->nQuery();
				$cols	=	array(
								"ID",
								"unique_id",
								"parent_id",
								"link",
								"menu_name",
								"group_id",
								"is_admin",
								"in_menubar",
								"full_path",
								"page_options",
								"page_live",
								'page_order'
							);
				$menus	=	$nquery	->select($cols)
									->from("main_menus")
									->orderBy(array("page_order"=>"ASC"))
									->fetch();
				
				$this->nApp->saveSetting('all_menus',$menus);
				
				return $menus;
			}
		
		public	function getUserInfo($username = false)
			{
				$username	=	trim($username);
				if(empty($username))
					return false;
				$nquery	=	$this->nApp->nQuery();
				$user	=	$nquery	->select()
									->from('users')
									->where(array('username'=>$username))
									->fetch();
				
				return ($user == 0)? false : $user;
			}
		
		public	function getUserCount()
			{
				try {
					$nquery	=	$this->nApp->nQuery();
					if(empty($nquery))
						throw new \Exception('Database is not working properly. User table likely missing.');
						
					$count	=	$nquery	->select('COUNT(*) as count')
										->from('users')
										->fetch(true);
											
					return $count['count'];
				}
				catch (\Exception $e) {
					if($this->nApp->isAdmin())
						die(printpre($e->getMessage(),'{backtrace}'));
				}
			}
		
		public	function getFileTypes()
			{
				$types	=	$this->nApp->nQuery()
								->select(array("file_extension"))
								->from('file_types')
								->where(array("page_live"=>'on'))
								->fetch();
				
				if($types == 0)
					return 0;
				
				return array_keys($this->organizeByKey($types,'file_extension'));
			}
	}