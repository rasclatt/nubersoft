<?php
// This will enable the nGet class to retrieve elements from the NubeData object
class	nApp
	{
		private	static $singleton;
		
		public	static	function getDataNode($key = false)
			{
				if(!empty($key) && isset(NubeData::$settings->{$key}))
					return NubeData::$settings->{$key};

				return false;
			}
		
		public	static	function autoload($func,$dir = false)
			{
				$nFunctions	=	new Nubersoft\nFunctions();
				$nFunctions->autoload($func,$dir);
			}
		
		public	static	function getUserId()
			{
				if(is_loggedin()) {
					return (isset($_SESSION['ID']))? $_SESSION['ID'] : false;
				}
				
				return false;
			}
		
		public	static	function getConStatus()
			{
				$con	=	self::getCore('connection');
				return (!empty($con->health));
			}
			
		// Fetches the site logo
		public	static	function getSiteLogo()
			{
				return self::nGet()->getSiteLogo();
			}
			
		public	static	function getBypass($key = false)
			{
				$nGet	=	self::getCore('bypass');

				if(empty($nGet)) {
					return self::setBypass($key);
				}

				if(!empty($key) && isset($nGet->{$key}))
					return $nGet->{$key};
				
				return $nGet;
			}
		
		private	static	function setBypass($type = false)
			{
				$array			=	self::getSitePrefs();
				$data			=	(isset($array->content))? $array->content:false;
				$new['login']	=	(!isset($data->login))? false:$data->login;
				$new['head']	=	(!isset($data->head))? false:$data->head;
				$new['menu']	=	(!isset($data->menu))? false:$data->menu;
				$new['foot']	=	(!isset($data->foot))? false:$data->foot;
				
				RegistryEngine::saveSetting('bypass',$new);
				
				if(!empty($new))
					return (isset($new[$type]))? Safe::to_object($new[$type]) : Safe::to_object($new);
				
				return false;
			}
		
		public	static	function menuValid()
			{
				$menu	=	self::getCore('menu');
				if(!empty($menu)) {
					return (!empty($menu->menu_struc));
				}
				
				return false;
			}
		
		public	static function getErrorTemplate($key = 404)
			{
				$core	=	self::getSite("error_{$key}");
			}
		
		public	static function getGlobalArr($type = 'post', $key = false)
			{
				$setPost	=	self::getGlobal($type);

				if(empty($setPost))
					return false;
				
				if(!empty($key))
					return (!empty($setPost->{$key}))? $setPost->{$key} : false;
				else
					return $setPost;

				return false;
			}
		
		private	static	function getGlobal($key = false)
			{
				$key	=	strtoupper("_{$key}");
				if(!empty($key) && empty(NubeData::$settings->{$key}))
					return false;
				
				if(!empty(NubeData::$settings->{$key}))
					return NubeData::$settings->{$key};

				return false;
			}
		
		public	static	function getPost($key = false)
			{
				return self::getGlobalArr('post',$key);
			}

		public	static	function getGet($key = false)
			{
				return self::getGlobalArr('get',$key);
			}
				
		
		public	static	function getRequest($key = false)
			{
				return self::getGlobalArr('request',$key);
			}
						
		public	static	function getExists($key = false)
			{
				$post	=	self::getGet($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
						
		public	static	function requestExists($key = false)
			{
				$post	=	self::getRequest($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
						
		public	static	function postExists($key = false)
			{
				$post	=	self::getPost($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
		
		public	static	function getIncidental($key = false)
			{
				$incidental	=	self::nGet()->getIncidentals();
				if(!empty($incidental)) {
					if(empty($key))
						return $incidental;
					else
						return (!empty($incidental->{$key}))? $incidental->{$key} : false;
				}
				
				return false;
			}
		
		public	static	function getError($key = false)
			{
				$errors	=	self::nGet()->getErrors();
				if(!empty($errors)) {
					if(empty($key))
						return $errors;
					else
						return (!empty($errors->{$key}))? $errors->{$key} : false;
				}
				
				return false;
			}
		
		public	static	function getUserInfo($username = false)
			{
				$username	=	trim($username);
				if(empty($username))
					return false;
				
				$user	=	self::nGet()->getUserInfo($username);
				
				if(!empty($user) && (isset($user[0]['password']))) {
					$len	=	strlen($user[0]['password']);
					$user[0]['password']	=	substr(str_pad(substr($user[0]['password'],-5),$len,"*",STR_PAD_LEFT),-20);
				}

				return Safe::to_object($user[0]);
			}
		
		public	static	function adminCheck($usergroup = false)
			{
				if(!is_numeric($usergroup))
					return false;
					
				if($usergroup === NBR_SUPERUSER)
					return true;
				elseif($usergroup <= NBR_ADMIN)
					return true;
				else
					return false;
			}
		
		public	static	function saveToLogFile($filename = false,$message = false)
			{
				if(!$filename || !$message)
					return false;

				self::nFunc()->autoload("write_file",NBR_FUNCTIONS);

				write_file(array("save_to"=>str_replace(DS.DS,DS,NubeData::$settings->site->temp_folder.DS."{$filename}"),"content"=>json_encode(array('debug'=>debug_backtrace(),'message'=>$message))));
			}
		
		public	static	function getPage($var = false)
			{
				return self::nGet()->getPage($var);
			}
		
		public	static	function getCachedStatus()
			{
				return self::nGet()->getPage("auto_cache");
			}
			
		public	static	function getSite($var = false)
			{
				return self::nGet()->getSite($var);
			}
		
		public	static	function getUser($var = false)
			{
				$user	=	self::getCore('user');
				
				if(empty($user))
					return false;

				if(!empty($var))
					return (!empty($user->{$var}))? $user->{$var} : false;
				else
					return $user;
			}

		public	static	function loggedInNotAdmin()
			{
				return (is_loggedIn() && !is_admin());
			}

		private static	function getCore($key = false)
			{
				return self::nGet()->getCoreElement($key);	
			}
		
		public	static	function getEngine($key = false)
			{
				$engine	=	self::getCore('engine');
				
				if(empty($engine))
					return false;
				
				if(!empty($key))
					return (isset($engine->{$key}))? $engine->{$key} : false;
				
				return $engine;
			}
		
		public	static	function getDbName()
			{
				$connection	=	self::getCore('connection');
				if(!empty($connection)) {
					$dbSet	=	(!empty($connection->database));
					return ($dbSet)? $connection->database : false;
				}
				
				return false;
			}
			
		public	static	function getHead()
			{
				$head	=	self::getCore('head');
				if(!empty($head)) {
					return (!empty($head))? $head : false;
				}
			}
			
		public	static	function getHeader($var = false)
			{
				$head	=	self::getHead();
				if(empty($head))
					return false;
				$hVar	=	(!empty($head->header))? $head->header : (object) array();
				return (!empty($var) && !empty($hVar->{$var}))? $hVar->{$var} : $hVar;
			}
			
		public	static	function getHeaderContent($var = false)
			{
				$head	=	self::getHeader('content');
				if(empty($head))
					return false;
				
				if(!empty($var) && !empty($head->{$var}))
					return $head->{$var};
				elseif(empty($var))
					return $head;
				else
					return false;
			}
		
		public	static	function getFooter()
			{
				$elem	=	self::getCore('prefs');
				return (!empty($elem->footer))? $elem->footer : false;
			}

		public	static	function getFooterContent($var = false)
			{
				$elem	=	self::getFooter();
				
				if(empty($elem->content))
					return false;

				if($var) {// && $var != 'html'
					return (!empty($elem->content->{$var}))? $elem->content->{$var} : false;
				}
				else
					return (!empty($elem->content))? $elem->content : false;
			}

		public	static	function getFavicons($var = false)
			{
				return Safe::decode(self::getHeaderContent('favicons'));
			}

		public	static	function getJavascript($var = false)
			{
				return Safe::decode(self::getHeaderContent('javascript'));
			}
		
		public	static	function getFileSalt()
			{
				$engine	=	self::getCore('engine');
				
				if(!empty($engine->file_salt))
					return $engine->file_salt;
				
				return false;
			}
			
		public	static	function getTableName()
			{
				$engine	=	self::getCore();
				if(!empty($engine->table_name))
					return $engine->table_name;
				
				return 'users';
			}
		
		public	static	function getSocialMedia($var = false, $not = array())
			{
				$elem	=	self::getFooterContent();
				$filter	=	array_merge(array("html"),$not);

				if(empty($elem))
					return false;
				
				if($var && !in_array($var,$filter)) {
					return (!empty($elem->{$var}))? $elem->{$var} : false;
				}
				else {
					if(empty($elem))
						return false;
					
					foreach($elem as $key => $value) {
						if(in_array($key,$filter))
							continue;
							
						$new[$key]	=	$value;
					}
					
					return (!empty($new))? Safe::to_object($new) : false;
				}
			}
		
		public	static	function getSiteContent()
			{
				$prefs	=	self::getSitePrefs();
				
				return (isset($prefs->content))? $prefs->content : false;
			}
		
		public	static	function getSitePrefs($refresh = false)
			{
				return self::nGet()->getSitePrefs($refresh);
			}
		
		public	static	function getRegistry($file = false)
			{
				$reg	=	NuberEngine::getRegFile($file);
				
				if(!empty($reg))
					return $reg;

				$getRemote	=	file_get_contents('http://www.nubersoft.com/client_assets/installer/registry.exemel');
				if(empty($getRemote))
					return false;
				// Load the file writer
				self::autoload('write_file',NBR_FUNCTIONS);
				// Save the file
				write_file(array("save_to"=>NBR_CLIENT_DIR.'/settings/registry.xml',"content"=>$getRemote));
				// Alert user
				die(nApp::getErrorLayout('noreg'));
			}
			
		public	static	function getPlugins()
			{
				if(!empty(self::$singleton['getPlugins']))
					return self::$singleton['getPlugins'];
					
				$plugin	=	self::getCore('plugin');
				
				if(!empty($plugin)) {
					return self::$singleton['getPlugins']	=	$plugin;
				}
				
				return self::$singleton['getPlugins'] = false;
			}
		
		public	static	function getTables()
			{
				if(!empty(NubeData::$settings->tables)) {
					return NubeData::$settings->tables;
				}
				else {
					if(self::getConStatus()) {
						$tables	=	organize(nQuery()->fetchTablesInDB()->fetch(),'Tables_in_'.self::getDbName());
						$tables	=	(!empty($tables))? array_keys($tables) : array();
						self::saveSetting('tables',$tables);
						return $tables;
					}
				}
			}
		
		public	static	function getDefaultTable()
			{
				// IF admin and on admin page
				$isAdmin	=	(is_admin() && self::isAdminPage());
				// If there is a get page
				$gTable		=	self::getGet('requestTable');
				$pTable		=	self::getPost('requestTable');
				$hasGet		=	(!empty($gTable));
				// If admin and get
				if($isAdmin && $hasGet)
					// If there is a POST table (for processing)
					$table	=	(!empty($pTable))? $pTable : $gTable;
				else
					$table	=	self::getTableName();
				
				self::resetTableAttr($table);
				
				return $table;
			}
		
		public	static	function tableValid($table = false)
			{
				if(empty($table))
					return false;
				$getTables	=	self::getTables();
				if(empty($getTables) || (!empty($getTables) && !is_array($getTables)))
					return false;
				
				return in_array($table,self::getTables());
			}
		
		
		public	static	function siteLive($refresh = false)
			{
				if(!$refresh) {
					if(!empty(self::$singleton['siteLive']))
						return self::$singleton['siteLive'];
				}
				
				// Return false by default
				self::$singleton['siteLive']	=	self::siteLiveStatus();
				
				return	self::$singleton['siteLive'];
			}
			
		public	static	function siteLiveStatus()
			{
				AutoloadFunction('silent_error');
				$site		=	self::getSitePrefs();
				// Register 404
				silent_error();
				return	(isset($site->content->site_live->toggle) && $site->content->site_live->toggle == 'on');
			}
		
		public	static	function siteValid()
			{
				return	(DatabaseConfig::$con != false);
			}
		
		public	static	function setSystemSettings()
			{
				self::getSystemSettings(true);
			}
		
		public	static	function getSystemSettings($refresh = false)
			{
				// Gets all prefs
				$all	=	self::getSitePrefs($refresh);
				// Returns just system prefs
				if(isset($all->preferences)) {
					return $all->preferences;
				}
			}
		
		public	static	function isAdminPage()
			{
				return (self::getPage('is_admin') == 1);
			}

		public	static	function isHomePage()
			{
				return (self::getPage('is_admin') == 2);
			}
		
		public	static	function getHomePage()
			{
				$menus	=	Safe::to_array(self::getAllMenus());
				
				if(!is_array($menus))
					return false;
					
				foreach($menus as $page) {
					if(!empty($page['is_admin'])) {
						if($page['is_admin'] === 2)
							return Safe::to_object($page);
					}
				}
			}
		
		public	static	function getAdminPage($key = false)
			{
				if(isset(NubeData::$settings->admin_page)) {
					if(!empty($key))
						return (isset(NubeData::$settings->admin_page->{$key}))? NubeData::$settings->admin_page->{$key} : false;
					else
						return	NubeData::$settings->admin_page;
				}
				
				self::nGet()->getAdminPage();
				
				return self::getAdminPage($key);
			}
		
		public	static	function saveSetting($val1,$val2)
			{
				RegistryEngine::saveSetting($val1,$val2);
			}
		
		public	static	function saveIncidental($val1,$val2)
			{
				RegistryEngine::saveIncidental($val1,$val2);
			}
		
		public	static	function saveError($val1,$val2)
			{
				RegistryEngine::saveError($val1,$val2);
			}
		
		public	static	function getColumns($table)
			{
				if(!empty(NubeData::$settings->{"columns_in_{$table}"})) {
					return NubeData::$settings->{"columns_in_{$table}"};
				}
					
				return self::nGet()->getColumns($table);
			}
			
		public	static	function getColumnInfo($table)
			{
				if(isset(NubeData::$settings->{"col_attr_in_".$table}))
					return NubeData::$settings->{"col_attr_in_".$table};
				else {
					self::getColumns($table);
					return (!empty(NubeData::$settings->{"col_attr_in_".$table}))? NubeData::$settings->{"col_attr_in_".$table} : false;
				}
			}
			
		private	static	function nGet()
			{
				return new nGet();
			}
		
		public	static	function getRoutingTables($table = false)
			{
				if(isset(NubeData::$settings->routing_tables)) {
					if(!empty($table))
						return (isset(NubeData::$settings->routing_tables->{$table}))? NubeData::$settings->routing_tables->{$table} : false;
					else
						return NubeData::$settings->routing_tables;
				}
				else {
					$tables	=	self::nGet()->getRoutingTables();
					
					if(!is_array($tables))
						return false;
					
					foreach($tables as $rows) {
						$tIds[$rows['table_name']]	=	$rows['table_id'];
					}
					
					self::saveSetting('routing_tables',((!empty($tIds))? $tIds : false));
	
					if(!empty($table))
						return (isset($tIds[$table]))? $tIds[$table] : false;
					else
						return (isset($tIds[$table]))? $tIds : false;
				}
			}
		
		public	static	function getPageURI()
			{
				$pageURI	=	(!empty(NubeData::$settings->pageURI))? Safe::to_array(NubeData::$settings->pageURI) : false;
				
				if($pageURI)
					return $pageURI;
				
				return self::nGet()->getPageURI();
			}
		
		public	static	function getDropDowns($table)
			{
				if(!empty(NubeData::$settings->{"dropdowns_{$table}"}))
					return NubeData::$settings->{"dropdowns_{$table}"};
				
				$drops	=	self::nGet()->getDropDowns($table);
				
				return $drops;
			}
		
		public	static	function getFormBuilder()
			{
				if(isset(NubeData::$settings->form_builder))
					return NubeData::$settings->form_builder;
	
				return self::nGet()->getFormBuilder();
			}
		
		public	static	function getAllMenus()
			{
				if(isset(NubeData::$settings->all_menus))
					return NubeData::$settings->all_menus;
	
				return self::nGet()->getAllMenus();
			}
		
		public	static	function getSessExpTime()
			{
				if(defined("SESSION_EXPIRE_TIME") && is_numeric(SESSION_EXPIRE_TIME))
					return SESSION_EXPIRE_TIME;
				elseif(!empty(NubeData::$settings->session_expire) && is_numeric(NubeData::$settings->session_expire))
					return NubeData::$settings->session_expire;
				else
					return 3500;
			}
		
		public	static	function getQueryCount()
			{
				return (isset(NubeData::$settings->engine->queries))? NubeData::$settings->engine->queries : false;
			}
		
		public	static	function getPageLike($val,$count = 1)
			{
				if(!empty(NubeData::$settings->menu_dir)) {
					$val	=	str_replace("!","",$val);
					$i = 1;
					foreach(NubeData::$settings->menu_dir as $menu) {
						if(preg_match("!".$val."!i",$menu)) {
							$matched[]	=	$menu;
							
							if(is_numeric($count) && ($i == $count))
								return ($i == 1)? implode("",$matched) : $matched;
								
							$i++;
						}
					}					
				}
				
				return (!empty($matched))? $matched : false;
			}
			
		public	static	function resetTableAttr($table = 'users')
			{
				NubeData::$settings->table_name 		=	$table;
				NubeData::$settings->engine->table		=	$table;
				NubeData::$settings->engine->table_name	=	$table;
			}
		
		public	static	function getCacheFolder()
			{
				return rtrim(self::getSite('cache_folder'),'/');
			}
		
		public	static	function getRequestTable($from = 'r')
			{
				switch($from) {
					case('r'):
						return self::getRequest('requestTable');
					case('p'):
						return self::getPost('requestTable');
					case('g'):
						return self::getGet('requestTable');
				}
			}
		
		public	static	function dirExists($dir = false,$make = false,$perm = 0755)
			{
				if(empty($dir))
					return false;

				AutoloadFunction("directory_exists");
				return directory_exists($dir,array("make"=>$make,"perm"=>$perm));
			}
		
		public	static	function stripRoot($value = false,$addSite = false)
			{
				$value	=	str_replace(NBR_ROOT_DIR,"",$value);
				
				return ($addSite)? str_replace(DS.DS,DS,site_url().$value) : $value;
			}
		
		public	static	function getAdminTxt()
			{
				$registry	=	self::getRegistry();
				if(!empty($registry['messaging']['forbid_access']))
					return $registry['messaging']['forbid_access'];
				else
					return 'Forbidden Access';
			}
		
		public	static	function getRunList()
			{	
				$arr['funcs']	=	self::runList();
				$arr['class']	=	self::runList(true);
				
				ob_start();
				echo printpre($arr);
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		private	static	function runList($use = false)
			{
				$filter	=	array('.','..');
				$fDir	=	($use)? scandir(NBR_CLASS_CORE) : scandir(NBR_FUNCTIONS);
				$rep	=	($use)? 'class' : 'function';
				$fDir	=	array_diff($fDir,$filter);
				
				foreach($fDir as $files) {
					preg_match("/(".$rep."\.)([^\.]{1,})(\.php)$/i",$files,$match).'<br />';
					if(empty($match[2]))
						continue;
					
					if(!$use) {
						if(function_exists($match[2]))
							$arr['active'][]	=	$match[2];
						else
							$arr['inactive'][]	=	$match[2];
					}
					else {
						if(class_exists($match[2]))
							$arr['active'][]	=	$match[2];
						else
							$arr['inactive'][]	=	$match[2];
					}
				}
				
				asort($arr['active'],SORT_NATURAL);
				asort($arr['inactive'],SORT_NATURAL);
				
				$arr['active']		=	array_values($arr['active']);
				$arr['inactive']	=	array_values($arr['inactive']);
				
				return $arr;
			}
		
		public	static function adminRestrict()
			{
				// See if loading page is an admin page
				$aPage		=	(!empty(nApp::getPage()->is_admin))? nApp::getPage()->is_admin : false;								
				// If the referring page is not admin page
				if($aPage != 1) {
					$allow	=	(defined("OPEN_ADMIN") && OPEN_ADMIN);
					// Check if allow from any-page-admin-login is set
					return $allow;
				}
				
				return true;
			}
		
		public	static	function getConfigs($location = false)
			{
				// Get the configs parser
				$parser	=	new Nubersoft\configFunctions(new \Nubersoft\nAutomator());
				// Add search locations
				$parser	->addLocation(NBR_CLIENT_DIR.DS.'settings')
						->addLocation(NBR_CLIENT_DIR.DS.'workflows')
						->addLocation(NBR_CLIENT_DIR.DS.'apps')
						->addLocation(NBR_TEMPLATE_DIR.DS.'default')
						->addLocation(NBR_CORE.DS.'workflows')
						->addLocation(NBR_CLIENT_DIR.DS.'template')
						->addLocation(NBR_ROOT_DIR.DS.'plugins');
				// This setting allows for the addition of new search locations
				if(is_array($location)) {
					//Loop through array and load
					foreach($location as $load) {
						$parser	->addLocation(\nApp::nAutomator()->matchFunction($load));
					}
				}
				// Parse and fetch xml array
				$regFiles	=	$parser->getConfigs();
				return (is_array($regFiles))? $regFiles : $regFiles;
			}
		
		public	static	function getErrorLayout($type = 'general')
			{
				if(is_file($err = NBR_RENDER_LIB."/assets/errors/error.{$type}.php")) {
					ob_start();
					include($err);
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
				else {
					return 'An Unknown error has occurred.';
				}
			}
		/*
		** @description	Returns a php to javascript library
		*/
		public	static	function jsEngine()
			{
				return new JsLibrary();
			}
		
		public	static	function getEmailer()
			{
				return new Emailer();
			}
		
		public	static	function cacheEngine()
			{
				return new BuildCache();
			}
		
		public	static	function userCount()
			{
				$nGet	=	new nGet();
				
				return $nGet->getUserCount();
			}
		
		public	static	function UserEngine()
			{
				return new Nubersoft\UserEngine();
			}
		
		public	static	function nFunc()
			{
				return new Nubersoft\nFunctions();
			}
			
		public	static	function nForm()
			{
				return new Nubersoft\nForm();
			}
		
		public	static	function nToken()
			{
				return new Nubersoft\nToken();
			}
		
		public	static	function nRegister()
			{
				return new Nubersoft\nRegister();
			}
		
		public	static	function nAutomator()
			{
				return new Nubersoft\nAutomator();
			}
			
		public	static	function nSession()
			{
				return new Nubersoft\nSessioner();
			}
			
		public	static	function nHtml()
			{
				return new Nubersoft\nHtml();
			}
			
		public	static	function con()
			{
				// Use either the persistant connection of the injected connection
				$db	=	(!is_object($settings))? \DatabaseConfig::getConnection() : $settings;
				if($override) {
					\DatabaseConfig::$con	=	null;
					\DatabaseConfig::$con	=	$db;
				}
				
				if($db) {
					return new \ConstructMySQL($db);
				}
				
				return   false;
			}
		
		public	static	function autoAddDefines()
			{
				$defines	=	self::nFunc()->getMatchedArray('register','define');
				if(is_array($defines)) {
					$defines	=	self::nFunc()->findKey($defines,'define')->getKeyList();
				}
				if(!empty($defines)) {
					foreach($defines as $inc) {
						$filename	=	self::nAutomator()->matchFunction($inc);
						if(is_file($filename))
							require_once($filename);
					}
				}
			}
		
		public	static	function nConfigFunc()
			{
				return new Nubersoft\configFunctions(new \Nubersoft\nAutomator());
			}
		
		public	static	function getConfigSetting($path,$configs = false)
			{
				$cEngine	=	self::nConfigFunc();
				
				if($configs)
					$cEngine->useArray($configs);
				
				return $cEngine->getSettings($path);
			}
		/*
		**	@description	Searches config for a whitelist
		**	@use			<whitelist>
		**						<admintools>
		**							<ip>12.123.12.123</ip>
		**						</admintools>
		**					</whitelist>
		*/
		public	static	function getWhiteList($type)
			{
				if(!is_string($type))
					return false;
					
				$whitelist	=	\nApp::getConfigSetting('whitelist_'.$type.'_ip');
				return (!empty($whitelist['ip']))? $whitelist['ip'] : false;
			}
		
		public	static	function onWhiteList($ip,$type = 'admintools')
			{
				// Get the whitelist
				$list	=	self::getWhiteList($type);
				// If not there
				if(!is_array($list) || empty($list)) {
					// Just warn there is no listing
					self::saveIncidental('whitelist_'.$type, array('whitelist_'.$type.'_warning'=>'no white list available for '.$type));
					// Return user allowed
					return true;
				}
				// If the value is returned but has a mixture of arrays and values
				if(isset($list[0]) && is_array($list[0])) {
					$new	=	array();
					// Loop through the list
					foreach($list as $ipSet) {
						// Filter values from arrays
						if(is_array($ipSet))
							$new	=	array_merge($ipSet,$new);
						else
							$new[]	=	$ipSet;
					}
					// Save to list value
					$list	=	$new;
				}
				
				return (in_array($ip,$list));
			}
		/*
		**	@description	This function pulls the data from the `file_types` table
		*/
		public	static	function getFileTypes()
			{
				if(empty(self::$singleton['file_types']))
					self::$singleton['file_types']	=	self::nGet()->getFileTypes();
				
				return self::$singleton['file_types'];
			}
		
		public	static	function getUploadDir($table,$settings = false)
			{
				$table		=	(!empty($table))? trim($table) : false;
				$append		=	(!isset($settings['append']) || !empty($settings['append']));
				$default	=	(!empty($settings['dir']))? $settings['dir'] : '/client_assets/images/default/';
				
				if(empty($table))
					return $default;
				
				$dir	=	nQuery()	->select("file_path")
										->from("upload_directory")
										->where(array("assoc_table"=>$table))
										->fetch();
				
				$path	=	($dir != 0)? $dir[0]["file_path"] : $default;
				
				return ($append)? str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.$path) : $path;
			}
	}