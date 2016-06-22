<?php
// This will enable the nGet class to retrieve elements from the NubeData object
class	nApp
	{
		private	static $singleton;
		
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
						
		public	static	function postExists($key = false)
			{
				$post	=	getPost($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
			
		public	static	function tokenExists($key = false)
			{
				return (isset($_SESSION['token'][$key]));
			}
			
		public	static	function clearToken($key = false)
			{
				if(isset($_SESSION['token'][$key])) {
					unset($_SESSION['token'][$key]);
					if(isset($_SESSION['token'][$key]))
						$_SESSION['token'][$key]	=	NULL;
				}
			}
		
		public	static	function getEmailer()
			{
				return new Emailer();
			}
		
		public	static	function getIncidental($key = false)
			{
				$nGet		=	new nGet();
				$incidental	=	$nGet->getIncidentals();
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
				$nGet	=	new nGet();
				$errors	=	$nGet->getErrors();
				if(!empty($errors)) {
						if(empty($key))
							return $errors;
						else
							return (!empty($errors->{$key}))? $errors->{$key} : false;
					}
				
				return false;
			}
		
		public	static	function getPage($var = false)
			{
				$nGet	=	new nGet();
				return $nGet->getPage($var);
			}
		
		public	static	function getCachedStatus()
			{
				$nGet	=	new nGet();
				return $nGet->getPage("auto_cache");
			}
			
		public	static	function getSite($var = false)
			{
				$nGet	=	new nGet();
				return $nGet->getSite($var);
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
				return self::getUser('admission');
			}

		private static	function getCore($key = false)
			{
				$nGet	=	new nGet();
				return $nGet->getCoreElement($key);	
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
				
				return (!empty($var) && !empty($head->header->{$var}))? $head->header->{$var} : $head->header;
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
				$nGet	=	new nGet();
				return $nGet->getSitePrefs($refresh);
			}
		
		public	static	function getRegistry($file = false)
			{
				return NuberEngine::getRegFile($file);
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
				$isAdmin	=	(is_admin() && self::isAdminPage());
				$hasGet		=	(!empty(self::getGet('requestTable')));
				return ($isAdmin && $hasGet)? self::getGet('requestTable') : self::getTableName();
			}
		
		public	static	function tableValid($table = false)
			{
				if(empty($table))
					return false;
				
				if(empty(self::getTables()) || (!empty(self::getTables()) && !is_array(self::getTables())))
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
				if($all->preferences) {
					return $all->preferences;
				}
			}
		
		public	static	function isAdminPage()
			{
				return self::getPage('is_admin');
			}
		
		public	static	function getAdminPage($return = false)
			{
				if(!empty(NubeData::$settings->admin_page))
					return	NubeData::$settings->admin_page;

				 return self::nGet()->getAdminPage();
			}
		
		public	static	function saveSetting($val1,$val2)
			{
				RegistryEngine::saveSetting($val1,$val2);
			}
		
		public	static	function getColumns($table)
			{
				if(isset(NubeData::$settings->{"columns_in_{$table}"}))
					return (is_array(NubeData::$settings->{"columns_in_{$table}"}))? Safe::to_array(NubeData::$settings->{"columns_in_{$table}"}) : false;
					
				return self::nGet()->getColumns($table);
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
		
		public	function getPageURI()
			{
				$pageURI	=	(!empty(NubeData::$settings->pageURI))? Safe::to_array(NubeData::$settings->pageURI) : false;
				
				if($pageURI)
					return $pageURI;
				
				return self::nGet()->getPageURI();
			}
		
		public	function getDropDowns($table)
			{
				if(isset(NubeData::$settings->{"dropdowns_{$table}"}))
					return NubeData::$settings->{"dropdowns_{$table}"};
	
				return self::nGet()->getDropDowns($table);
			}
		
		public	function getFormBuilder()
			{
				if(isset(NubeData::$settings->form_builder))
					return NubeData::$settings->form_builder;
	
				return self::nGet()->getFormBuilder();
			}
	}