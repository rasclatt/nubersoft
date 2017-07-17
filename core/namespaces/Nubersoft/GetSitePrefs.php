<?php
namespace Nubersoft;

class GetSitePrefs extends \Nubersoft\nApp
	{
		private	static	$prefs;
		private	$client_define;
		
		public	function fetch($fetch = false)
			{
				if(!empty($this->getDataNode('preferences')))
					return $this->getDataNode('preferences');
				
				if(is_file($preferences))
					return json_decode(file_get_contents($preferences),true);
				
				return $this->set($fetch);
			}
		
		public	function set($fetch = false)
			{
				$GetSitePrefs	=	$this;
				$getPrefs		=	$this->getPrefFile('preferences',array('save'=>(!$this->isAjaxRequest())),false,function($path,$nApp) use ($GetSitePrefs,$fetch) {
					try {
						if($fetch) {
							if(!is_array($fetch)) {
								$fetch	=	array($fetch);
							}
						}
						else {
							$fetch	=	array('settings_head','settings_site','settings_foot');
						}
						
						$sql		=	"SELECT * FROM `system_settings`
											WHERE `page_element` = '".implode("' OR `page_element` = '",$fetch)."'
											LIMIT 3";
						$con		=	$nApp->nQuery();
						$fetchData	=	$con->query($sql)->getResults();
						$getPrefs	=	$nApp->organizeByKey($fetchData,'page_element');
						
					}
					catch (\PDOException $e) {
						die($e->getMessage());
					}
					catch (\Exception $e) {
						die($e->getMessage());
					}
					
					if(empty($getPrefs))
						return false;
					
					foreach($getPrefs as $type => $data) {
						$getPrefs[$type]['content']	=	json_decode(htmlspecialchars_decode($data['content'],ENT_QUOTES));
					}
					
					return	$getPrefs;
				});
				
				$this->saveSetting('preferences',$getPrefs);
				
				if(empty($getPrefs)) {
					if($this->isAdmin())
						echo printpre($getPrefs);
					//\Nubersoft\Flags\Controller::create('maintenance');
					throw new \Exception('Your system preferences are not available.',200);
				}
				
				return $this;
			}
			
		public	function getCacheFile($name = 'preferences',$type='json')
			{
				return $this->toSingleDs($this->getCacheFolder().DS.'prefs'.DS.$name.".{$type}");
			}
		
		public	function setDefines($location = false)
			{
				# Add in the client define
				$this->setClientDefine()->includeDefine();
				# See if there are any other defines to include
				if(!$location)
					$location	=	$this->getSettingsDir('defines.xml');
				# If there is no define file, continue
				if(!is_file($location))
					return;
				# Try and retrieve config array from defines
				$getDefines	=	$this->getPrefFile('defines',array('xml'=>pathinfo($location,PATHINFO_DIRNAME),'save'=>false),false,function($path,$nApp){
					$configs	=	$nApp->toArray($nApp->getHelper('nRegister')->parseXmlFile($path));
					if(empty($configs))
						return false;
					# Set storage array
					$getDefines	=	array();
					# Extract defines from client settings
					$nApp->extractAll($configs, $getDefines);
					# If there are no values, stop
					if(empty($getDefines))
						return;
					# Get the automator
					$nAutomator	=	$nApp->getHelper('nAutomator',$this);
					# Process the file paths
					$getDefines	=	array_map(function($v) use ($nAutomator) {
						return $nAutomator->matchFunction($v);
					},$getDefines);
					
					return $getDefines;
				});
				# Stop if none
				if(empty($getDefines))
					return;
				# Include all the defines
				$this->autoInclude($getDefines);
			}
		
		public	function autoInclude($array)
			{
				$i = 1;
				foreach($array as $spot) {
					if(!is_file($spot))
						continue;
					
					include_once($spot);
					$i++;
				}
				
				
			}
		
		public	function getDefaultClient($chain = false)
			{
				$this->client_define	=	 $this->toSingleDs($this->getCacheFolder().DS.'config-client.php');
	
				return ($chain)? $this->client_define : $this;
			}
		
		public	function getClientDefine()
			{
				return $this->client_define;
			}
		
		public	function setClientDefine($dir = false)
			{
				$cacheReg	=	NBR_CLIENT_SETTINGS.DS.'cache_dir.pref';
				
				if(!defined('CACHE_DIR') && empty($dir)) {
					$cacheLink	=	(is_file($cacheReg))? file_get_contents($cacheReg) : $this->getCacheDirFromRegistry($cacheReg);
					if(!empty($cacheLink))
						$this->saveSetting('site',array('cache_folder'=>rtrim($cacheLink,DS)));
				}
				
				$dir	=	(!empty($dir))? rtrim($dir,DS).DS : $this->toSingleDs($this->getCacheFolder().DS);
				
				$this->client_define	=	$this->toSingleDs($dir.DS.'config-client.php');
				return $this;
			}
		/*
		**	@description	Creates a user config define file. Created based on the registry file
		*/
		public	function getClientDefinesFromRegistry($registry = false)
			{
				$getDefines	=	$this->defines();
				# If it exists, save the file and return the path
				if(!empty($getDefines)) {
					# Autoload the functions folder
					$this->autoloadContents(NBR_FUNCTIONS);
					$nAutomator	=	$this->getHelper('nAutomator');
					foreach($getDefines as $const => $value) {
						if(!is_bool($value)) {
							if(empty($value))
								continue;
						}
						
						if(strtolower($value) == 'true')
							$value	=	true;
						elseif(strtolower($value) == 'false')
							$value	=	false;
						
						if(!is_bool($value) && !is_numeric($value))
							$value	=	'"'.$value.'"';
						else {
							if(is_bool($value))
								$value	=	($value == true)? 'true' : 'false';
						}
						
						if(is_array($value))
							die(printpre($value));
						
						$defines[]	=	'if(!defined("'.$const.'"))
	define("'.$const.'"'.','.$nAutomator->matchFunction($value).');';
					}
					return $defines;
				}
			}
		
		public	function defines()
			{
				if(empty($registry)) {
					# Paths to client and base registry files
					$finder[]	=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
					$finder[]	=	NBR_SETTINGS.DS.'registry.xml';
					foreach($finder as $path) {
						if(!is_file($path))
							continue;
						
						$reg	=	self::call('nRegister')->parseXmlFile($path);
						break;
					}
				}
				else {
					$reg	=	$file;
				}
				
				$cacheDir	=	$this->getMatchedArray(array('ondefine'),'',$this->toArray($reg));
				# If it exists, save the file and return the path
				if(!empty($cacheDir['ondefine'][0])) {
					foreach($cacheDir['ondefine'][0] as $const => $value) {
						if($value == 'true')
							$value	=	true;
						elseif($value == 'false')
							$value	=	false;
						
						$result		=	$this->getHelper('nAutomator',$this)->matchFunction($value);
						$defines[strtoupper($const)]	=	$result;
					}
					
					return $defines;
				}
			}
		
		/*
		**	@description	Attempts to pull the default cache directory right from registry file
		**					Once it finds a usable path it will save it to a pref file
		*/
		public	function getCacheDirFromRegistry($cacheReg=false)
			{
				# Name of the key that should be in your registry file
				$name		=	'cache_dir';
				# Paths to client and base registry files
				$finder[]	=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
				$finder[]	=	NBR_SETTINGS.DS.'registry.xml';
				# Checks first if there is some other path to save to set
				$cacheReg	=	(empty($cacheReg))? NBR_CLIENT_SETTINGS.DS.$name.'.pref' : $cacheReg;
				# Loops through the reg paths
				foreach($finder as $reg) {
					# If the pathe exists try to get the defined path
					if(is_file($reg)) {
						$cacheDir	=	$this->getMatchedArray(array('ondefine',$name),'',self::call('nRegister')->parseXmlFile($reg));
						# If it exists, save the file and return the path
						if(!empty($cacheDir[$name][0])) {
							$cacheLink	=	$cacheDir[$name][0];
							# Save pref file
							$this->saveFile($cacheLink,$cacheReg);
							return $cacheLink;
						}
					}
				}
			}
		/*
		**	@description	This will include the default client define
		*/
		public	function includeDefine()
			{
				if(!is_file($this->client_define)) {
					$getDefine	=	$this->getPlugin('nPlugins\Nubersoft\CoreStartUp')->createDefine($this);
					$txt		=	($getDefine)? 'Client prefs have been rebuilt.' : 'An error occurred rebuilding prefs';
					$this->saveIncidental('rebuild_reg',array('msg'=>$txt));
					if(is_file($this->client_define))
						include_once($this->client_define);
				}
				else {
					include_once($this->client_define);
				}
			}
		
		public	function setDatabase()
			{
				# See if the database is good
				$prefs	=	array(
					'engine'=>(DatabaseConfig::connect() instanceof \PDO),
					'users_table'=>(($this->nQuery()->query("show tables like 'users'")->getResults() != 0))
				);
				
				$this->saveSetting('database',$prefs);
			}
		/*
		**	@desription	This will push to error page immediately
		*/
		public	function getOfflineStatus($nRender = false)
			{
				$success	=	true;
				# If the database connection is not working
				if(empty($this->getData()->getConnection()->health))
					$success	=	false;
				# If the site is in maintenance and the page is not the admin page
				if(!$this->siteLive() && !$this->isAdminPage())
					# If the user is not admin, not success
					$success	=	($this->isAdmin());
				# If the page is not live
				if(empty($this->getDataNode('pageURI')->page_live)) {
					# If not admin or the page is invalid
					if(!$this->isAdmin() || empty($this->getDataNode('pageURI')->ID))
						$success	=	false;
				}
				# If the page is valid but not on
				elseif($this->getDataNode('pageURI')->page_live != 'on') {
					# If the user is not an admin user
					if(!$this->isAdmin()) {
						$success	=	false;
					}
				}
				# If not successful run the error page
				if(!$success) {
					$script	=	$this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('_SERVER')->SCRIPT_URL);
	
					if(!is_file($script) && !is_dir($script)) {
						self::call('nObserverTemplate')->offline($nRender);
						exit;
					}
				}
			}
		
		public	function setRegistry()
			{
				$config	=	$this->getPrefFile('registry',array('save'=>true),false,function($path,$nApp) {
					$name	=	'registry';
					# Get registry file
					$config	=	NBR_CLIENT_SETTINGS.DS."{$name}.xml";
					# If there is no reg file
					if(!is_file($config)) {
						# If there is a core file
						if(is_file($base = NBR_SETTINGS.DS."{$name}.xml")) {
							# Get the core, make folder, save a copy to client
							if($nApp->isDir(NBR_CLIENT_SETTINGS.DS)) {
								if(!@copy($base,$config))
									throw new \Exception('You have no registry and one could not be copied for you.');
							}
						}
					}
					# Parse registry file
					$cParse	=	$nApp->getHelper('nRegister')->parseXmlFile($config);
					# If the reg can not be parsed throw error
					if(empty($cParse))
						throw new \Exception('There is a problem with your registry file.');
					
					return $cParse;
				});
				
				# Store to data node
				$this->saveSetting('registry',$config);
				
				return $this;
			}
		
		public	function setSiteUri()
			{
				$this->getHelper('nGet')->getPageURI();
				
				if(empty($this->getDataNode('pageURI')->ID)) {
					$reg	=	$this->getData()->getRegistry();
					
					if(empty($reg))
						$this->setRegistry();
					
					$getMsg			=	$this->toArray($this->getData()->getRegistry(array('messaging','error404')));
					$msg['title']	=	(!empty($getMsg['title']))? $getMsg['title']: 'Page not found.';
					$msg['body']	=	(!empty($getMsg['body']))? $getMsg['body']: 'Error 404.';	
					
					$this->saveSetting('error404',$msg);
					$this->saveError('error404',$msg);
					$this->saveIncidental('error404',$msg);
				}
				else
					$this->getHelper('NubeData')->clearNode('error404');
				
				return $this;
			}
		
		public	function setCallMethod()
			{
				$this->saveSetting('site',array('call_type'=>(($this->isAjaxRequest())? 'ajax' : 'http' )));
				return $this;
			}
		
		public	function setCallTables()
			{
				$this->saveSetting('site',array(
					'load_table'=>$this->safe()->sanitize($this->getPost('requestTable')),
					'view_table'=>$this->safe()->sanitize($this->getGet('requestTable'))));
				
				return $this;
			}
		
		public	function autoFunctions()
			{
				$prefs	=	$this->toSingleDs($this->getCacheFolder().DS.'prefs'.DS.'autoloads.json');
				$cached	=	file_exists($prefs);
				if($cached)
					$new	=	json_decode(file_get_contents($prefs));
				else {
					$config	=	$this->getMatchedArray(array('actions','blockflow_preferences','autoload'),'',$this->getDataNode('registry'));
					$new		=	array();
					if(!isset($config['autoload']))
						return;
						
					$this->flattenArray($config['autoload'],$new);
					# Save prefs file to disk
					$this->savePrefFile('autoloads',$new);
				}
				
				if(!empty($new)) {
					# Autload
					foreach($new as $function => $find) {
						if(function_exists($function))
							continue;
							
						if(!empty($find)) {
							if(is_file($inc = $this->getHelper('nAutomator',$this)->matchFunction($find)))
								require_once($inc);
						}
						else
							$this->autoload($function);
					}
				}
			
				return $this;
			}
		
		public	function setCurrentAction()
			{
				$this->saveSetting('site',array('action'=>$this->getRequest('action')));
				return $this;
			}
		
		public	function setPageRequestSettings()
			{
				$site		=	$this->getPageURI();
				$nTemplate	=	$this->getHelper('nTemplate');
				$isAdmin	=	(!empty($site['is_admin']) && $site['is_admin'] == 1);
				$isHome		=	(!empty($site['is_admin']) && $site['is_admin'] == 2);
				
				if(!empty($site['template']))
					$pageTemplate	=	array(
						'dir'=>$siteDirTemp = DS.trim($site['template'],DS),
						'frontend'=>$siteDirTemp.DS.'frontend',
						'backend'=>$siteDirTemp.DS.'admintools'
					);
				else
					$pageTemplate	=	false;
				
				$siteTemplate	=	array(
					'dir'=>$siteDirTemp = DS.trim($this->getSiteTemplate(),DS),
					'frontend'=>$siteDirTemp.DS.'frontend',
					'backend'=>$siteDirTemp.DS.'admintools'
				);
				
				$defTemplate	=	array(
					'dir'=>$siteDirTemp = DS.trim($this->stripRoot(NBR_TEMPLATE_DIR.DS.'default'),DS),
					'frontend'=>$siteDirTemp.DS.'frontend',
					'backend'=>$siteDirTemp.DS.'admintools'
				);
				
				$templateBase	=	array(
					'dir'=>$siteDirTemp = DS.trim($this->stripRoot(NBR_CLIENT_DIR.DS.'template'),DS),
					'frontend'=>$siteDirTemp.DS.'frontend',
					'backend'=>$siteDirTemp.DS.'admintools'
				);
				
				$settings	=	array(
					'page'=>$site,
					'admin_page'=>$isAdmin,
					'home_page'=>$isHome,
					'page_valid'=>(!empty($site->ID)),
					'templates'=>array(
						'template_page'=>((is_array($pageTemplate))? $pageTemplate : $siteTemplate),
						'template_base'=>$templateBase,
						'template_site'=>$siteTemplate,
						'template_default'=>$defTemplate,
						'has_global_template' =>(!is_array($pageTemplate))
					),
					'default_template_dir'=>$this->getDefaultTemplate(),
					'template_frontend'=>$this->getDefaultTemplate().DS.'frontend',
					'template_admintools'=>$this->getDefaultTemplate().DS.'admintools'
				);
				
				$settings['template_current']	=	($isAdmin)?
						$this->getTemplatePathMatch('index.php','backend',$settings['templates']) : 
						$this->getTemplatePathMatch('index.php','frontend',$settings['templates']);
				
				$this->saveSetting('site',$settings);
				
				return $this;
			}
		/*
		**	@description	Sets headers
		*/
		public	function setHeaders()
			{
				$args	=	func_get_args();
				# Header options
				# Allow html submission
				$xss	=	($this->getHelper('UserEngine')->isAdmin())? "X-XSS-Protection: 0" : "X-XSS-Protection: 1; mode=block";
				$this->getHelper('nRouter')->addHeader($xss);
				# If there are additional, add them
				if(!empty($args[0]))
					$this->getHelper('nRouter')->addHeader($args[0]);
				
				return $this;
			}
		
		public	function setPasswordEngine()
			{
				$default	=	PasswordGenerator::USE_DEFAULT;
				PasswordGenerator::Engine($default);
				$this->saveSetting('site',array('password_engine'=>$default));
				return $this;
			}
		
		public	function setCurrentTime()
			{
				$settings	=	array(
					'server_time'=>date('Y-m-d H:i:s'),
					'server_timezone'=>$this->setAppTimeZone()
				);
				
				$this->saveSetting('site',$settings);
				
				return $this;
			}
		
		public	function setAppTimeZone()
			{
				# Get the current timezone of the application
				$timezone	=	$this->getHelper('nLocale')->getTimeZone(true);
				# Set the timezone
				$this->getHelper('Settings')->setTimeZone($timezone);
				# Return the value
				return $timezone;
			}
		/*
		**	@description	Checks if there is a user and menu table
		*/
		public	function setInstallStatus()
			{
				$hasAdmin	=	$this->getHelper('UserEngine')->hasAdminAccounts();
				$this->saveSetting('site',array('has_admin'=>$hasAdmin));
				$hasMPref	=	$this->getPrefFile('main_menus');
				if($hasMPref)
					return;
				# Check if there is an admin menu
				$sql		=	"SELECT COUNT(*) as count FROM `main_menus` WHERE `is_admin` = 1";
				$hasMenus	=	$this->nQuery()->query($sql)->getResults(true);
				# Add not an admin menu
				if($hasMenus['count'] == 0)
					$this->getPlugin('\nPlugins\Nubersoft\CoreInstaller')->installDefaultMenu($this);
			}
		
		public	function getSetToken(\Nubersoft\nToken $nToken)
			{
				$deliver	=	(!empty($this->getPost()->data->deliver))? $this->toArray($this->getPost()->data->deliver) : false;
				$fullArray	=	false;
				
				if(!empty($deliver)) {
					foreach($deliver as $key => $value) {
						if(is_array($value)) {
							foreach($value as $name) {
								$fullArray[$key.'_'.$name]	=	$nToken->setMultiToken($key,$name);
							}
						}
						else
							$fullArray[$key.'_'.$value]	=	$nToken->setMultiToken($key,$value);
					}
				}
				
				$login	=	(!empty($_SESSION['token']['login']))? $_SESSION['token']['login'] : false;
				$page	=	(!empty($_SESSION['token']['nProcessor']['page']))? $_SESSION['token']['nProcessor']['page'] : false;
				
				if(!$login) {
					$login	=	$nToken->getSetToken('login');
				}
				if(!$page) {
					$page	=	$nToken->setMultiToken('nProcessor','page');
				}
				
				$isAjax	=	$this->isAjaxRequest();
				
				if($isAjax)
					die(json_encode(array('login'=>$login,'nProcessor'=>$page,'request'=>$fullArray)));
			}
	}