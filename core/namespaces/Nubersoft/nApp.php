<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;
/*
**	This will enable the nGet class to retrieve elements from the NubeData object as well as return any number
** 	of important elements. If a method is not in this class, it will be called from __callStatic()
*/
class	nApp extends \Nubersoft\nFunctions
	{
		protected	static	$site_info;
		protected	$Cache;
		/*
		**	@description	Trims down the url of leading and trailing forward slashes
		*/
		private	function trimUrl($name,$url)
			{
				$url	=	(!empty($url))? '/'.trim($url,'/') : '';
				return '/'.$name.$url;
			}
		/*
		**	@description	Fetches the media folder path including http
		*/
		public	function mediaUrl($url = false,$force = false)
			{
				return $this->siteUrl($this->trimUrl('media',$url),array('locale'=>false),$force);
			}
		/*
		**	@description	Fetches the images folder path including http
		*/
		public	function imagesUrl($url = false,$force = false)
			{
				return $this->mediaUrl($this->trimUrl('images',$url),$force);
			}
		/*
		**	@description	Fetches the css folder path including http
		*/
		public	function cssUrl($url = false,$force = false)
			{
				return $this->mediaUrl($this->trimUrl('css',$url),$force);
			}
		/*
		**	@description	Fetches the js folder path including http
		*/
		public	function jsUrl($url = false,$force = false)
			{
				return $this->mediaUrl($this->trimUrl('js',$url),$force);
			}
		/*
		**	@description	Fetches the url including the locale
		*/
		public	function localeUrl($url = false,$force = false)
			{
				return $this->siteUrl($url,$force);
			}
		/*
		**	@description	Fetches the url inside the client folder
		*/
		public	function clientUrl($url,$force=false)
			{
				return $this->siteUrl('/client'.$url,$force);
			}
		/*
		**	@description	Fetches the url inside the client/media folder
		*/
		public	function cMediaUrl($url,$force=false)
			{
				return $this->clientUrl('/media'.$url,$force);
			}
		/*
		**	@description	Fetches the url inside the client/media/images folder
		*/
		public	function cImagesUrl($url,$force=false)
			{
				return $this->cMediaUrl('/images'.$url,$force);
			}
		/*
		**	@description	Fetches the url inside the client/media/css folder
		*/
		public	function cCssUrl($url,$force=false)
			{
				return $this->cMediaUrl('/css'.$url,$force);
			}
		/*
		**	@description	Fetches the url inside the client/media/js folder
		*/
		public	function cJsUrl($url,$force=false)
			{
				return $this->cMediaUrl('/js'.$url,$force);
			}
			
		public	function isSsl($force = false)
			{
				if(!isset(self::$site_info))
					self::$site_info	=	self::call('nRequester')->isSsl($force);
				
				return self::$site_info;
			}
		/*
		**	@description	Fetches the site url
		*/
		public	function siteUrl()
			{
				$useAppend	=	false;
				$args		=	func_get_args();
				if(!empty($args)) {
					foreach($args as $arg) {
						if(is_bool($arg))
							$forceSSL	=	$arg;
						elseif(is_string($arg))
							$url	=	$arg;
						elseif(is_array($arg)) {
							if(isset($arg['locale']))
								$useAppend	=	$arg['locale'];
						}
					}
				}
				
				$co			=	$this->getSession('LOCALE');
				$append		=	($useAppend && !empty($co))? $co : ''; //die($this->isSsl());
				$forceSSL	=	(!isset($forceSSL))? false : $forceSSL;
				$url		=	(!isset($url))? $append.'' : $append.$url;
				$baseUrl	=	(defined("BASE_URL"))? BASE_URL : 'http://'.$_SERVER['HTTP_HOST'];
				$sslUrl		=	(defined("BASE_URL_SSL"))? BASE_URL_SSL : 'https://'.$_SERVER['HTTP_HOST'];
				$forceSSL	=	(defined("FORCE_URL_SSL"))? FORCE_URL_SSL : $forceSSL;
				
				if($forceSSL)
					return $sslUrl.$url;
				else
					return ($this->isSsl())? $sslUrl.$url : $baseUrl.$url;
			}
		/*
		**	@description	Returns the parsed url but can retreive any parse_url() type found at:
		**					http://php.net/manual/en/function.parse-url.php
		*/
		public	function siteHost($settings = false)
			{
				$settings	=	(!empty($settings))? $settings : PHP_URL_HOST;
				return parse_url($this->localeUrl(),$settings);
			}
		/*
		**	@description	Multi-domain comparison
		*/
		public	function isSiteHost($site,$parse=true)
			{
				if($parse)
					$site	=	parse_url($site,PHP_URL_HOST);
				
				return ($this->siteHost() == $site);
			}
		/*
		**	@description	This will retrieve any stored data in the NubeData data object
		**	@param	$key	[string]	This will return a first-layer node from the data object
		*/
		public	function getDataNode($key = false,$type = false)
			{
				if((stripos($type,'warn') !== false) || (stripos($type,'incident') !== false))
					$useData	=	$this->nGet()->getIncidentals();
				elseif(stripos($type,'error') !== false)
					$useData	=	$this->nGet()->getErrors();
				else
					# Use nGet to retrieve an element from data array
					return $this->nGet()->getCoreElement($key);
					
				if(!empty($key))
					return (!empty($useData->{$key}))? $useData->{$key} : false;
				
				return $useData;
			}
		/*
		**	@description	Sets a public data node to dyanamic node (Methodize)
		**	@param	$key	[string]	Value from the data node to set to methodize
		*/
		public	function toNode()
			{
				# Check if node available
				$args	=	func_get_args();
				# No value indicates full data node
				$value	=	(!empty($args[0]))? $args[0] : false;
				# Get the data node
				$node		=	$this->getDataNode($value);
				# If the element is an object data set, then array it
				if(is_object($node))
					$node	=	$this->toArray($node);
				# Create instance and save the node
				$Methodize	=	new Methodize();
				if(empty($value))
					$value	=	'data_node';
				# Save the data to an attribute
				$Methodize->saveAttr($value,$node);
				# Return it back for use
				return $Methodize->{$value}();
			}
		/*
		**	@description	This will check in the NubeData data object to see if it's available
		*/
		public	function issetDataNode($key)
			{
				# Use nGet to retrieve an element from data array
				return $this->nGet()->issetCoreElement($key);
			}
		/*
		**	@description	This will check in the NubeData data object to see if it's available
		*/
		public	function deleteDataNode($key)
			{
				# Use nGet to retrieve an element from data array
				if($this->nGet()->issetCoreElement($key)){
					unset(NubeData::$settings->{$key});
				}
				
				return ($this->nGet()->issetCoreElement($key));
			}
		/*
		**	@description	This will override first-level NubeData data objects
		*/
		public	function resetDataNodeVals($array)
			{
				foreach($array as $key => $values) {
					foreach($values as $skey => $sval) {
						\NubeData::$settings->{$key}->{$skey}	=	(is_array($sval))? $this->toObject($sval) : $sval;
					}
				}
			}

		public	function getRealSession($key = false)
			{
				if(!isset($_SESSION))
					return false;
				
				if(!empty($key)) {
					if(is_array($key)) {
						return $this->getMatchedArray($key,'false',$_SESSION);
					}
					else {
						return (!empty($_SESSION[$key]))? $_SESSION[$key] : false;
					}
				}
				
				return $_SESSION;
			}
		/*
		**	@description	Get sessionized user id
		*/
		public	function getUserId()
			{
				return $this->getSession('ID');
			}
		/*
		**	@description	Get the status of the database connection
		*/
		private static	function getCore($key = false)
			{
				return $this->getDataNode($key);	
			}
		/*
		**	@description	Get the status of the database connection
		*/
		public	function getConStatus()
			{
				$con	=	$this->getDataNode('connection');
				return (!empty($con->health));
			}
		# Fetches the site logo
		public	function getSiteLogo()
			{
				return $this->nGet()->getSiteLogo();
			}
			
		public	function getBypass($key = false)
			{
				$nGet	=	$this->getDataNode('bypass');

				if(empty($nGet)) {
					return $this->setBypass($key);
				}

				if(!empty($key) && isset($nGet->{$key}))
					return $nGet->{$key};
				
				return $nGet;
			}
		
		public	function setBypass($type = false)
			{
				$array			=	$this->getSitePrefs();
				$data			=	(isset($array->content))? $array->content:false;
				$new['login']	=	(!isset($data->login))? false:$data->login;
				$new['head']	=	(!isset($data->head))? false:$data->head;
				$new['menu']	=	(!isset($data->menu))? false:$data->menu;
				$new['foot']	=	(!isset($data->foot))? false:$data->foot;
				
				$this->saveSetting('bypass',$new);
				
				if(!empty($new))
					return (isset($new[$type]))? $this->toObject($new[$type]) : $this->toObject($new);
				
				return false;
			}
		
		public	function menuValid()
			{
				$menu	=	$this->getDataNode('menu');
				if(!empty($menu)) {
					return (!empty($menu->menu_struc));
				}
				
				return false;
			}
		
		public function getErrorTemplate($key = 404)
			{
				$core	=	$this->getSite("error_{$key}");
			}
		
		public	function fetchTemplate()
			{
				return $this->getHelper('nTemplate');
			}
		
		public function getGlobalArr($type = 'post', $key = false)
			{
				$setPost	=	$this->getGlobal($type);

				if(empty($setPost))
					return false;
				
				if(!empty($key))
					return (!empty($setPost->{$key}))? $setPost->{$key} : false;
				else
					return $setPost;

				return false;
			}
		/*
		**	@description	Sanitizes input and saves it to data nodes
		*/
		private	function getGlobal($key = false)
			{
				$key	=	strtoupper("_{$key}");
				
				if(!isset(NubeData::$settings->{$key}))
					$this->getHelper('Submits')->sanitize();
				
				if(!empty($key) && empty($this->getDataNode($key)))
					return false;
				
				if(!empty($this->getDataNode($key)))
					return $this->getDataNode($key);

				return false;
			}
		
		public	function getRawPost($key = false)
			{
				return $this->getGlobalArr('raw_post',$key);
			}
		
		public	function getRawGet($key = false)
			{
				return $this->getGlobalArr('raw_get',$key);
			}
		
		public	function getPost($key = false,$toObj = false)
			{
				$arr	=	$this->getGlobalArr('post',$key);
				
				if($toObj) {
					$Methodize	=	new Methodize();
					foreach($this->toArray($arr) as $skey => $value)
						$Methodize->saveAttr($skey,$value);
					
					unset($arr);
					
					return $Methodize;
				}
				else {
					return $arr;
				}
			}

		public	function getGet($key = false)
			{
				return $this->getGlobalArr('get',$key);
			}
		/*
		**	@description	Node version of getSession()
		*/
		public	function getSessionNode()
			{
				$SESSION	=	$this->toArray($this->getDataNode('_SESSION'));
				$Methodize	=	new Methodize();
				$Methodize->saveAttr('session',$SESSION);
				return $Methodize->session();
			}
		/*
		**	@description	Fetches the session array (or value)
		*/
		public	function getSession($key = false,$remove = false)
			{
				$array	=	$this->toArray($this->getDataNode('_SESSION'));
				
				if(empty($array))
					return false;
				
				if(!empty($key)) {
					if(is_array($key)) {
						if($remove)
							trigger_error('You can not remove session values in bulk.',E_USER_NOTICE);
						
						$value	=	$this->toObject($this->getMatchedArray($key,false,$array));
						return $value;
					}
					else {
						if($remove)
							$this->getHelper('nSessioner')->destroy($key);
	
						return (!empty($array[$key]))? $this->toObject($array[$key]) : false;
					}
				}
				
				return $this->toObject($array);
			}
		
		public	function getRequest($key = false)
			{
				return $this->getGlobalArr('request',$key);
			}
						
		public	function getExists($key = false)
			{
				$post	=	$this->getGet($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
						
		public	function requestExists($key = false)
			{
				//$post	=	$this->getRequest($key);
				$post	=	$this->getRequest();
				
				return (!empty($post) && isset($post->{$key}));
			}
						
		public	function postExists($key = false)
			{
				$post	=	$this->getPost($key);
				
				return (!empty($post) && isset($post->{$key}));
			}
		
		public	function getIncidental($key = false)
			{
				$incidental	=	$this->nGet()->getIncidentals();
				if(!empty($incidental)) {
					if(empty($key))
						return $incidental;
					else
						return (!empty($incidental->{$key}))? $incidental->{$key} : false;
				}
				
				return false;
			}
		/*
		**	@description	Saves errors to session and errors or incidentals data node
		*/
		public	function toAlert($msg, $action = 'general', $opts = false, $type = true, $toSess = true)
			{
				if($this->isAjaxRequest())
					$this->ajaxAlert($msg);
					
				$msgArr	=	array('msg'=>$msg);
				$array	=	(is_array($opts) && !empty($opts))? array_merge($msgArr,$opts) : $msgArr;
				if($type)
					$this->saveIncidental('alerts',array($action=>$array));
				else
					$this->saveError('alerts',array($action=>$array));
				
				if($toSess)
					$this->setSession('alerts',array($action=>$array),true);
			}
		/*
		**	@description	Saves errors to session and errors or incidentals data node
		*/
		public	function toError($msg, $action = 'general', $opts = false, $toSess = true)
			{
				$this->toAlert($msg, $action, $opts, false, $toSess);
			}
		/*
		**	@description	Retrieves errors from session and errors or incidentals data node
		*/
		public	function getAlert($key,$type = false)
			{
				$errs	=	($type)? $this->getError('alerts') : $this->getIncidental('alerts');
				$sErrs	=	$this->getSession('alerts');
				
				if(!empty($errs)) {
					if(!empty($sErrs)) {
						$errs	=	array_merge($this->toArray($errs),$this->toArray($sErrs));
					}
				}
				elseif(!empty($sErrs)) {
					$errs	=	$this->toArray($sErrs);
				}
				
				if(empty($errs))
					return false;
					
				$errors	=	$this->getMatchedArray(array($key,'msg'),'',$errs);
				
				return (!empty($errors['msg']) && is_array($errors['msg']))? array_unique($errors['msg']) : $errors;
			}
		
		public	function getError($key = false)
			{
				$errors	=	$this->nGet()->getErrors();
				if(!empty($errors)) {
					if(empty($key))
						return $errors;
					else
						return (!empty($errors->{$key}))? $errors->{$key} : false;
				}
				
				return false;
			}
		
		public	function adminCheck($usergroup = false)
			{
				if(empty($usergroup))
					return false;
					
				return self::getClass('\Nubersoft\UserEngine')->groupIsAdmin($usergroup);
			}
			
		public	function getCachedStatus()
			{
				return $this->nGet()->getPage("auto_cache");
			}

		public	function loggedInNotAdmin($username = false)
			{
				return $this->getHelper('UserEngine')->isLoggedInNotAdmin($username);
			}

		public	function isLoggedIn($username = false)
			{
				return $this->getHelper('UserEngine')->isLoggedIn($username);
			}
		
		public	function getUserInfo($username = false)
			{
				$username	=	trim($username);
				if(empty($username))
					return false;
				
				$user	=	$this->nGet()->getUserInfo($username);
				
				if(!empty($user) && (isset($user[0]['password']))) {
					$len	=	strlen($user[0]['password']);
					$user[0]['password']	=	substr(str_pad(substr($user[0]['password'],-5),$len,"*",STR_PAD_LEFT),-20);
				}

				return $this->toObject($user[0]);
			}
		
		public	function saveToLogFile($filename = false,$message = false,$opts = false)
			{
				$this->getHelper('nLogger')->saveToLog($filename,$message,$opts);
			}
		
		public	function getPageById($id,$key=false)
			{
				$find	=	'*';
				if(!empty($key))
					$find	=	'`'.implode('`,`',$key).'`';
					
				$sql	=	"SELECT {$find} FROM `main_menus` WHERE ID = :0";
				return $this->nQuery()->query($sql,array($id))->getResults(true);
			}
		
		public	function getPage($var = false)
			{
				$pageURI	=	$this->getDataNode('pageURI');
				if(!empty($pageURI)) {
					if(!empty($var))
						return (!empty($pageURI->{$var}))? $pageURI->{$var} : false;
						
					return $pageURI;
				}
			}
		
		public	function getTemplate()
			{
				return $this->getSite('template_current');
			}
		
		public	function getSite($var = false)
			{
				$this->getHelper('GetSitePrefs')->setPageRequestSettings();
				return $this->nGet()->{"getSite"}($var);
			}
		
		public	function getUser($var = false)
			{
				$user	=	$this->getDataNode('user');
				
				if(empty($user))
					return false;

				if(!empty($var))
					return (!empty($user->{$var}))? $user->{$var} : false;
				else
					return $user;
			}
		
		public	function Users()
			{
				return $this->getHelper('User');
			}
		
		public	function getEngine($key = false)
			{
				$engine	=	$this->getDataNode('engine');
				
				if(empty($engine))
					return false;
				
				if(!empty($key))
					return (isset($engine->{$key}))? $engine->{$key} : false;
				
				return $engine;
			}
		
		public	function getDbName()
			{
				$connection	=	$this->getDataNode('connection');
				if(!empty($connection)) {
					$dbSet	=	(!empty($connection->database));
					return ($dbSet)? $connection->database : false;
				}
				
				return false;
			}
			
		public	function getHead()
			{
				return $this->getPreferences('head');
			}
			
		public	function getHeader($var = false)
			{
				$head	=	$this->getHead();
				if(empty($head))
					return false;
				
				if(!empty($var))
					return (isset($head->{$var}))? $head->{$var} : false;
				
				return $head;
			}
			
		public	function getHeaderContent($var = false)
			{	
				$head	=	$this->getPrefsContent('head');
				
				if(empty($head))
					return false;
				
				if(!empty($var) && !empty($head->{$var}))
					return $head->{$var};
				elseif(empty($var))
					return $head;
				else
					return false;
			}
		
		public	function getPreferences($val = 'site')
			{
				$prefs	=	$this->getDataNode('preferences');
				$array	=	(isset($prefs->{"settings_{$val}"}))? $prefs->{"settings_{$val}"} : false;
				if(!empty($array)) {
					return (!empty($array))? $array : false;
				}
			}
		
		public	function getFooter()
			{
				return $this->getPreferences('foot');
			}

		public	function getFooterContent($var = false)
			{
				$elem	=	$this->getFooter();
				
				if(empty($elem->content))
					return false;

				if($var) {# && $var != 'html'
					return (!empty($elem->content->{$var}))? $elem->content->{$var} : false;
				}
				else
					return (!empty($elem->content))? $elem->content : false;
			}

		public	function getFavicons($var = false)
			{
				return self::getClass('\Nubersoft\Safe')->decode($this->getHeaderContent('favicons'));
			}

		public	function getJavascript($var = false)
			{
				return self::getClass('\Nubersoft\Safe')->decode($this->getHeaderContent('javascript'));
			}
		
		public	function getFileSalt()
			{
				if(!empty($this->getDataNode('site')->open_ssl_salt))
					return $this->getDataNode('site')->open_ssl_salt;
				elseif(defined('OPENSSL_SALT') && OPENSSL_SALT)
					$salt	=	(string) OPENSSL_SALT;
				elseif(is_file($file = $this->getCacheFolder().DS.'encryption'.DS.'open_ssl_salt.pref'))
					$salt	=	(string) file_get_contents($file);
				else
					$salt	=	false;
				
				$this->saveSetting('site',array('open_ssl_salt'=>$salt));
				
				return $salt;
			}
			
		public	function getTableName()
			{
				$engine	=	$this->getDataNode();
				if(!empty($engine->table_name))
					return $engine->table_name;
				
				return 'users';
			}
		
		public	function getSocialMedia($var = false, $not = array())
			{
				$elem	=	$this->getFooterContent();
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
					
					return (!empty($new))? $this->toObject($new) : false;
				}
			}
		
		public	function getSiteContent()
			{
				$prefs	=	$this->getSitePrefs();
				
				return (isset($prefs->content))? $prefs->content : false;
			}
		/*
		**	@description	Creates the site preferences
		*/
		protected	function createPrefs()
			{
				self::call("GetSitePrefs")->set();
			}
		
		public	function getPrefsContent($type = 'site')
			{
				$prefs	=	$this->getPreferences($type);
				return (!empty($prefs->content))? $prefs->content : false;
			}
		
		public	function getSitePrefs($refresh = false)
			{
				if(!empty($this->getPrefsContent('site')))
					$site	=	$this->getPrefsContent('site');
				else {
					$this->createPrefs();
					$site	=	$this->getPrefsContent('site');
				}
				
				return $this->toObject($site);
			}
		/*
		**	@description	Retrieve the reg file specifically
		*/
		public	function getRegistry($key = false)
			{
				$Cache	=	$this->nCache();
				# See if stored in the load
				if(!empty($this->getDataNode('registry')) && !empty($this->getCacheFolder()) && $Cache->allowCacheRead()) {
					$reg	=	$this->toArray($this->getDataNode('registry'));
					if(!empty($key))
						return (!empty($reg[$key]))? $reg[$key] : false;
					
					return $reg;
				}
				# Cached file for registry
				$cache	=	$this->toSingleDs($this->getCacheFolder().DS.'registry.json');
				# If there is a registry cached
				if(is_file($cache) && $Cache->allowCacheRead()) {
					# Decode and send back
					$decode	=	json_decode(file_get_contents($cache),true);
					# Save to settings
					if(empty($this->getDataNode('registry')))
						$this->saveSetting('registry',$decode);
					if($key)
						return (isset($decode[$key]))? $decode[$key] : false;
					return $decode;
				}
				# Default location for registry file
				$file	=	NBR_CLIENT_DIR.DS.'settings'.DS.'registry.xml';
				# If not found
				if(!is_file($file)) {
					# Let it be known
					throw new nException('No registry found.',404001);
					return false;
				}
				# Parse the xml
				$reg	=	$this->getHelper('nRegister')->parseXmlFile($file);
				# Set the default path to the cache folder
				$cDir	=	str_replace(NBR_ROOT_DIR,'',NBR_CLIENT_DIR).DS.'settings'.DS.'cachefiles';
				# If reg is parsed
				if(!empty($reg)) {
					# If there is a previous define
					if(defined('CACHE_DIR'))
						$cDir	=	CACHE_DIR;
					else {
						# Try and extract cache dir from reg file
						$cDirFind	=	$this->getMatchedArray(array('ondefine','cache_dir'),false,$reg);
						# If there is a cache folder, assign it
						if(!empty($cDirFind['cache_dir'][0]))
							$cDir	=	$cDirFind['cache_dir'][0];
					}
				}
				# Build the path to cache
				$cDir	=	NBR_ROOT_DIR.DS.trim($cDir,DS).DS;
				# Strip out any double forward slashes
				$cache	=	$this->toSingleDs($cDir.DS.'registry.json');
				# If not empty
				if(!empty($reg)) {
					$opts	=	array(
									'content'=>json_encode($reg),
									'save_to'=>$cache,
									'secure'=>true,
									'overwrite'=>true
								);
					# Save to disk as json
					if($Cache->allowCacheRead() && !$this->isAjaxRequest())
						$this->getHelper('nFileHandler')->writeTofile($opts);
					# Save to settings
					$this->saveSetting('registry',$reg);
					# Return
					$reg	=	$this->toArray($this->getDataNode('registry'));
					if(!empty($key))
						return (!empty($reg))? $reg[$key] : false;
					
					return $reg;
				}
			}
		/*
		**	@description	This method gets the paths to search out for new config files
		**	@param	[object] This will be an instance of a parser class (nRegister() is default)
		*/
		public	function getLoadZones($xmlParser = false)
			{
				# See if this node is available
				$loadZones		=	$this->getDataNode('loadzones');
				# If available, return it
				if(!empty($loadZones))
					return $this->toArray($loadZones);
				# Assign Parser
				$xmlParser		=	(is_object($xmlParser))? $xmlParser : new nRegister();
				# Common zone file path
				$zoneFilePath	=	'settings'.DS."register".DS.'config.xml';
				# Try parsing the core loadzone
				$regfile		=	NBR_CORE.DS.$zoneFilePath;
				# Client loadzone
				$cRegfile		=	NBR_CLIENT_DIR.DS.$zoneFilePath;
				# Parse xml
				$loadZoneNbr	=	$xmlParser->parseXmlFile($regfile);
				# Try and parse client loadzone
				$loadZoneClient	=	(is_file($cRegfile))? $xmlParser->parseXmlFile($cRegfile) : false;
				# Create instance of nFunctions()
				$nFunc			=	$this;
				# Create a parsing function for strings returned by xml
				$combineConf	=	function($array,&$new) use ($nFunc)
					{
						$zones	=	array_keys($array['loadzones']);
						$nAuto	=	new \Nubersoft\nAutomator($this);
						foreach($zones as $zone) {
							$new[$zone]	=	$nFunc->getMatchedArray(array('loadzones',$zone),'_',$array);
							foreach($new[$zone][$zone] as $val) {
								if(is_array($val)) {
									foreach($val as $subVal) {
										$string[$zone][]	=	$subVal;
									}
								}
								else
									$string[$zone][]	=	$val;
							}
							
							if(isset($string[$zone]))
								$new[$zone][$zone]	=	$string[$zone];
							
							if(!empty($new[$zone][$zone])) {
								$packed		=	array_map(function($v) use ($nAuto) {
									return $nAuto->matchFunction($v);
								},$new[$zone][$zone]);
								
								$new[$zone]	=	$packed;
							}
						}
					};
				$core	=	
				$client	=	array();
				$combineConf($loadZoneNbr,$core);
				if(!empty($loadZoneClient)) {
					$combineConf($loadZoneClient,$client);
				}
				# Get unique categories from both client and core arrays
				$looper	=	array_unique(array_merge(array_keys($core),array_keys($client)));
				# Set a final array to store the paths
				$final	=	array();
				foreach($looper as $title) {
					if(isset($core[$title])) {
						if(isset($client[$title]))
							$final[$title]	=	array_merge($core[$title],$client[$title]);
						else
							$final[$title]	=	$core[$title];
					}
					elseif(isset($client[$title])) {
						$final[$title]	=	$client[$title];
					}
					
					if(isset($final[$title]))
						$final[$title]	=	array_unique($final[$title]);
				}
				# Check if there is a cache pause on
				$allow		=	$this->nCache()->allowCacheRead();
				# Allow saving if no cache pause is active and request is not ajax
				if($allow && !$this->isAjaxRequest()) {
					# Save to file
					$this->savePrefFile('loadzones',$final);
					# Save loadzones to data array
					$this->saveSetting('loadzones',$final);
				}
				# Return the values
				return $final;
			}
		
		public	function getConfigs()
			{
				# Get args
				$args		=	func_get_args();
				# Set the location for the file
				$location	=	(!empty($args[0]))? $args[0] : false;
				# Fetch the paths of where configs can load from
				$zones		=	$this->getLoadZones();
				# Get the configs parser
				$parser		=	new configFunctions(new nAutomator($this));
				# If there are pages to loop through
				if(!empty($zones)) {
					foreach($zones as $title => $zoneArr) {
						# If a loadzone is empty, skip
						if(empty($zoneArr))
							continue;
						# If a set of data is available, loop through it
						foreach($zoneArr as $loadspots) {
							$parser	->addLocation($loadspots);
						}
					}
				}
				# This setting allows for the addition of new search locations
				if(is_array($location)) {
					//Loop through array and load
					foreach($location as $load) {
						$parser	->addLocation($this->getHelper('nAutomator',$this)->matchFunction($load));
					}
				}
				# Parse and fetch xml array
				$regFiles	=	$parser->getConfigsArr();
				return (is_array($regFiles))? $regFiles : $regFiles;
			}
			
		public	function getPlugins()
			{
				if(!empty($this->singleton['getPlugins']))
					return $this->singleton['getPlugins'];
					
				$plugin	=	$this->getDataNode('plugin');
				
				if(!empty($plugin)) {
					return $this->singleton['getPlugins']	=	$plugin;
				}
				
				return $this->singleton['getPlugins'] = false;
			}
		/*
		**	@description	Main return for the database connection
		*/
		public	function nQuery($con = false)
			{
				$con	=	(!($con instanceof \Nubersoft\QueryEngine))? $this->getHelper('ConstructMySQL') : $con;
				# Check if the connect is a valid database connection
				if(!($con instanceof \Nubersoft\ConstructMySQL)) {
					# Check if there are database credentials
					if(!$this->getDbCredsFile())
						# Run a first run page
						$view	=	$this->render(NBR_CORE.DS.'settings'.DS.'firstrun'.DS.'database.php');
					else {
						# If there is a problem with the connection, show under construction
						$view	=	$this->getHelper('nRender')->getTemplateDoc('static.offline.php');
						# Save the log to file
						$this->saveToLogFile('nquery_error','Database connection is invalid. No instance of \Nubersoft\QueryEngine or \Nubersoft\ConstructMySQL');
					}
					
					die($view);
				}
				else
					return $con;
			}
		
		public	function getDbCredsFile()
			{
				$path	=	NBR_CLIENT_DIR.DS.'settings'.DS.'dbcreds.php';
				return (is_file($path))? $path : false;
			}
		
		public	function getTables()
			{
				$tables	=	$this->getDataNode('tables');
				if(empty($tables)) {
					if($this->getConStatus()) {
						$database	=	$this->getHelper('FetchCreds')->getData();
						$dbTables	=	$this->nQuery()->fetchTablesInDB($database)->getResults();
						$tables		=	array();
						if(is_array($dbTables)) {
							$this->flattenArrayByKey($dbTables,$tables,'Tables_in_'.$database);
							$this->saveSetting('tables',$tables);
						}
					}
				}
				
				return $tables;
			}
		/*
		**	@description	Checks if the user, either by value passed or by session, is an admin
		*/
		public	function isAdmin($usergroup = false)
			{
				# If the value input is not empty, if it's a string, get the constant equivalent
				if(!empty($usergroup))
					$usergroup	=	(is_string($usergroup))? $this->convertUserGroup($usergroup) : $usergroup;
				# If not set, try and get it from session
				else
					$usergroup	=	$this->getNode('_SESSION')->getUsergroup();
				# If either method fails, return false
				if(empty($usergroup))
					return false;
				# Get the admin status
				return $this->getHelper('UserEngine')->groupIsAdmin($usergroup);
			}
		/*
		**	@description	Matches current usergroup
		*/
		public	function isGroupMember($usergroup = 'webuser',$prepend = 'nbr_')
			{
				# Fetch the constant
				$usergroup	=	constant(strtoupper($prepend.$usergroup));
				# If the usergroup constant not set, stop
				if($usergroup === false)
					return false;
					
				return ($this->getNode('_SESSION')->getUsergroup() == $usergroup);
				
			}
		/*
		**	@description	Fetches my usergroup
		*/
		public	function getMyGroup($def = false)
			{
				$group	=	$this->getNode('_SESSION')->getUsergroup();
				return (empty($group))? $def : $group;
			}
		
		public	function getDefaultTable()
			{
				# See if the user is an admin
				$userAdmin	=	self::getClass('\Nubersoft\UserEngine')->isAdmin();
				# See if this page is an admin page
				$pageAdmin	=	$this->isAdminPage();
				# If admin and on admin page
				$isAdmin	=	($userAdmin && $pageAdmin);
				# If there is a get page
				$gTable		=	$this->safe()->sanitize($this->getGet('requestTable'));
				$pTable		=	$this->safe()->sanitize($this->getPost('requestTable'));
				$hasGet		=	(!empty($gTable));
				# If admin and get
				if($isAdmin && $hasGet)
					# If there is a POST table (for processing)
					$table	=	(!empty($pTable))? $pTable : $gTable;
				else
					$table	=	$this->getTableName();
				
				$this->resetTableAttr($table);
				
				return $table;
			}
		
		public	function tableValid($table = false)
			{
				if(empty($table))
					return false;
				$getTables	=	$this->getTables();
				if(empty($getTables) || (!empty($getTables) && !is_array($getTables)))
					return false;
				
				return in_array($table,$this->getTables());
			}
		
		
		public	function siteLive()
			{
				$site	=	$this->getSitePrefs();
				return	(!empty($site->site_live->toggle) && $site->site_live->toggle == 'on');
			}
			
		public	function siteLiveStatus()
			{
				$site	=	$this->getSitePrefs();
				return	(isset($site->content->site_live->toggle) && $site->content->site_live->toggle == 'on');
			}
		
		public	function siteValid()
			{
				return	(DatabaseConfig::$con != false);
			}
		
		public	function setSystemSettings()
			{ 
				$this->getSystemSettings(true);
			}
		
		public	function getSystemSettings($refresh = false)
			{
				# Gets all prefs
				$all	=	$this->getSitePrefs($refresh);
				# Returns just system prefs
				if(isset($all->preferences)) {
					return $all->preferences;
				}
			}
		
		public	function isAdminPage()
			{
				return ($this->getPage('is_admin') == 1);
			}

		public	function isHomePage()
			{
				return ($this->getPage('is_admin') == 2);
			}
		
		public	function getHomePage()
			{
				$menus	=	$this->toArray($this->getAllMenus());
				
				if(!is_array($menus))
					return false;
					
				foreach($menus as $page) {
					if(!empty($page['is_admin'])) {
						if($page['is_admin'] === 2)
							return $this->toObject($page);
					}
				}
			}
		
		public	function getAdminPage($key = false)
			{
				if(isset(NubeData::$settings->admin_page)) {
					if(!empty($key))
						return (isset(NubeData::$settings->admin_page->{$key}))? NubeData::$settings->admin_page->{$key} : false;
					else
						return	NubeData::$settings->admin_page;
				}
				
				$this->nGet()->getAdminPage();
				
				return $this->getAdminPage($key);
			}
			
		public	function adminUrl($key = false)
			{
				$adminPage	=	rtrim($this->getAdminPage('full_path'),'/');
				
				return $this->siteUrl($adminPage).$key;
			}
		
		public	function saveSetting($val1,$val2,$clear = false)
			{
				if($clear)
					RegistryEngine::app()->clearDataNode($val1,"settings");
				
				RegistryEngine::saveSetting($val1,$val2);
			}
		
		public	function saveIncidental($val1,$val2,$clear = false)
			{
				if($clear)
					RegistryEngine::app()->clearDataNode($val1,"incidentals");
				
				RegistryEngine::saveIncidental($val1,$val2);
			}
		
		public	function saveError($val1,$val2,$clear = false)
			{
				if($clear)
					RegistryEngine::app()->clearDataNode($val1,"errors");
					
				RegistryEngine::saveError($val1,$val2);
			}
		
		public	function getColumns($table)
			{
				$columns	=	$this->getDataNode("columns_in_{$table}");
					
				return (!empty($columns))? $columns : $this->nGet()->getColumns($table);
			}
			
		public	function getColumnInfo($table)
			{
				$attr	=	$this->getDataNode("col_attr_in_{$table}");
				if(empty($attr)) {
					$this->getColumns($table);
					$attr	=	$this->getDataNode("col_attr_in_{$table}");
				}
				
				return (!empty($attr))? $attr : false;
			}
			
		private	static	function nGet()
			{
				return self::call('nGet');
			}
		
		public	function getRoutingTables($table = false)
			{
				if(isset(NubeData::$settings->routing_tables)) {
					if(!empty($table))
						return (isset(NubeData::$settings->routing_tables->{$table}))? NubeData::$settings->routing_tables->{$table} : false;
					else
						return NubeData::$settings->routing_tables;
				}
				else {
					$tables	=	$this->nGet()->getRoutingTables();
					
					if(!is_array($tables))
						return false;
					
					foreach($tables as $rows) {
						$tIds[$rows['table_name']]	=	$rows['table_id'];
					}
					
					$this->saveSetting('routing_tables',((!empty($tIds))? $tIds : false));
	
					if(!empty($table))
						return (isset($tIds[$table]))? $tIds[$table] : false;
					else
						return (isset($tIds[$table]))? $tIds : false;
				}
			}
		/*
		**	@description	Fetches the current page
		*/
		public	function getPageURI($key=false)
			{
				$page	=	$this->getDataNode('pageURI');
				
				if(empty($page))
					$page	=	$this->nGet()->getPageURI();
				
				if(!empty($key))
					return (!empty($page->{$key}))? $page->{$key} : false;
				
				return (!empty($page))? $this->toArray($page) : false;
			}
		
		public	function getDropDowns($table)
			{
				$dropdowns	=	$this->getDataNode("dropdowns_{$table}");
				
				if(!empty($dropdowns))
					return $dropdowns;
				
				$drops	=	$this->nGet()->getDropDowns($table);
				
				return $drops;
			}
		
		public	function getFormBuilder()
			{
				$form	=	$this->issetDataNode('form_builder');
				if($form)
					return $this->getDataNode('form_builder');
	
				return $this->nGet()->getFormBuilder();
			}
		
		public	function getAllMenus()
			{
				$set	=	$this->issetDataNode('all_menus');
				if($set)
					return $this->getDataNode('all_menus');
	
				return $this->nGet()->getAllMenus();
			}
		
		public	function getSessExpTime()
			{
				$expire	=	$this->getDataNode('session_expire');
				
				if(defined("SESSION_EXPIRE_TIME") && is_numeric(SESSION_EXPIRE_TIME))
					return SESSION_EXPIRE_TIME;
				elseif(is_numeric($expire))
					return $expire;
				else
					return 3500;
			}
		
		public	function getQueryCount()
			{
				$engine	=	$this->getDataNode('engine');
				return (isset($engine->queries))? $engine->queries : false;
			}
		
		public	function getPageLike($val,$count = 1)
			{
				$menuDir	=	$this->getDataNode('menu_dir');
				if(!empty($menuDir)) {
					$val	=	str_replace("!","",$val);
					$i = 1;
					foreach($menuDir as $menu) {
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
			
		public	function resetTableAttr($table = 'users')
			{
				$this->saveSetting('table_name',$table);
				NubeData::$settings->engine->table		=	$table;
				NubeData::$settings->engine->table_name	=	$table;
			}
		/*
		**	@description	Returns the designated settings folder
		*/
		public	function getSettingsDir($append = false)
			{
				return (!empty($append))? $this->toSingleDs(NBR_CLIENT_SETTINGS.DS.$append) : NBR_CLIENT_SETTINGS;
			}
		/*
		**	@description	Returns the designated cache folder
		*/
		public	function getCacheFolder($append = false)
			{
				# Cache pref location
				$cache	=	$this->getSettingsDir('cache_dir.pref');
				# See if the cache has already pulled and return it
				if(!empty($this->getDataNode('site')->cache_dir)) {
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('site')->cache_dir.DS.$append),DS);
					return $cachePath;
				}
				# If there is a define, use it first
				elseif(defined('CACHE_DIR') && !empty(constant('CACHE_DIR'))) {
					$this->saveSetting('site',array('cache_dir'=>CACHE_DIR));
					# Trim the right side and remove any double forward slashes
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.CACHE_DIR.DS.$append),DS);
					return $cachePath;
				}
				# If no define exists, the try and extract the cached one
				elseif(is_file($cache)) {
					$cacheContent	=	@file_get_contents($cache);
					$this->saveSetting('site',array('cache_dir'=>$cacheContent));
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.$cacheContent.DS.$append),DS);
					return $cachePath;
				}
				# If no define or cache file is found, create a cache file
				else {
					# Try and get the client reg file but if not found use base
					$getRegFunc	=	function() use ($append)
						{
							$path[]		=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
							$path[]		=	NBR_SETTINGS.DS.'registry.xml';
							
							foreach($path as $spot) {
								if(!is_file($spot))
									continue;
									
								$reg	=	$this->getMatchedArray(array('ondefine','cache_dir'),'',$this->getHelper('nRegister')->parseXmlFile($spot));
								
								if(!empty($reg['cache_dir'][0]))
									return rtrim($this->toSingleDs($reg['cache_dir'][0].DS.$append),DS);
							}
						};
					# Run the above anon function to get the path for the cache file
					$cachePath	=	$getRegFunc();
					# Save to data node
					$this->saveSetting('site',array('cache_dir'=>$cachePath));
					# Save the pref file. Have to use this instead of savePrefFile() because it
					# runs into a loop
					$this->saveFile(rtrim($cachePath,DS),$cache);
					# Return the folder from the settings
					$cachePath	=	trim($this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('site')->cache_dir.DS.$append),DS);
					return $cachePath;
				}
			}
		
		public	function getRequestTable($from = 'r')
			{
				switch($from) {
					case('r'):
						return $this->safe()->sanitize($this->getRequest('requestTable'));
					case('p'):
						return $this->safe()->sanitize($this->getPost('requestTable'));
					case('g'):
						return $this->safe()->sanitize($this->getGet('requestTable'));
				}
			}
		
		public	function stripRoot($value = false,$addSite = false)
			{
				$value	=	str_replace(NBR_ROOT_DIR,"",$value);
				
				return ($addSite)? $this->toSingleDs($this->siteUrl($value)) : $value;
			}
		
		public	function getAdminTxt()
			{
				$registry	=	$this->getRegistry();
				if(!empty($registry['messaging']['forbid_access']))
					return $registry['messaging']['forbid_access'];
				else
					return 'Forbidden Access';
			}
		
		public	function getRunList()
			{	
				$arr['funcs']	=	$this->runList();
				$arr['class']	=	$this->runList(true);
				
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
		
		public function adminRestrict()
			{
				# See if loading page is an admin page
				$aPage		=	(!empty($this->getPage()->is_admin))? $this->getPage()->is_admin : false;								
				# If the referring page is not admin page
				if($aPage != 1) {
					$allow	=	(defined("OPEN_ADMIN") && OPEN_ADMIN);
					# Check if allow from any-page-admin-login is set
					return $allow;
				}
				
				return true;
			}
		
		public	function getErrorLayout($type = 'general')
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
		**	@description	This method just checks if the force render is set.
		**					This will allow the page to keep rendering with defaults if true
		*/
		public	function forceRender()
			{
				return $this->getMode('force',true);
			}
		/*
		** @description	Returns a php to javascript library
		*/
		public	function jsEngine()
			{
				return self::getClass('\JsLibrary');
			}
		
		public	function getEmailer()
			{
				return self::getClass('\Emailer');
			}
		
		public	function cacheEngine()
			{
				return self::getClass('\Nubersoft\BuildCache');
			}
		
		public	function userCount()
			{
				$nGet	=	new nGet();
				
				return $nGet->getUserCount();
			}
		
		public	function nFunc()
			{
				return self::getClass('\Nubersoft\\'.str_replace('_','',__FUNCTION__).'tions');
			}
			
		public	function nSession()
			{
				return self::getClass('\Nubersoft\\'.str_replace('_','',__FUNCTION__).'er');
			}
		
		public	function con()
			{
				# Use either the persistant connection of the injected connection
				$db	=	(!is_object($settings))? DatabaseConfig::getConnection() : $settings;
				if($override) {
					DatabaseConfig::$con	=	null;
					DatabaseConfig::$con	=	$db;
				}
				
				if($db) {
					return self::getClass('\ConstructMySQL',$db);
				}
				
				return   false;
			}
		
		public	function autoAddDefines()
			{
				$defines	=	$this->getMatchedArray('register','define');
				if(is_array($defines)) {
					$defines	=	$this->findKey($defines,'define')->getKeyList();
				}
				if(!empty($defines)) {
					foreach($defines as $inc) {
						$filename	=	$this->getHelper('nAutomator',$this)->matchFunction($inc);
						if(is_file($filename))
							require_once($filename);
					}
				}
			}
		
		public	function nConfigFunc()
			{
				# This uses __callStatic and a one-object injector. If more objects are required for injection,
				# you must use self::getClass('object\name')
				# $this->nAutomator() also uses __callStatic().
				# self::getClass('\Nubersoft\nAutomator') also will return the same object
				return $this->getHelper('configFunctions',$this->getHelper('nAutomator',$this));
			}
		
		public	function getConfigSetting($path,$configs = false)
			{
				$cEngine	=	$this->nConfigFunc();
				
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
		public	function getWhiteList($type)
			{
				if(!is_string($type))
					return false;
				
				$searchArr	=	array('whitelist',$type,'ip');
				$sName		=	'nbr_'.implode('_',$searchArr);
				$whitelist	=	$this->getConfigSetting($searchArr);
				$dbList		=	$this->getPrefFile($sName,array('save'=>true),false,function($path,$nApp) use ($sName){
					$ipList	=	$nApp->nQuery()->query("select `content` from `components` where `ref_spot` = :0 and `page_live` = 'on'",array($sName))->getResults();
					if($ipList != 0)
						return array_keys($nApp->organizeByKey($ipList,'content',array('multi'=>true)));
					
					return array();
				});
				
				$ips	=	(!empty($whitelist['ip']))? $whitelist['ip'] : false;
				
				if(!empty($ips)) {
					if(!empty($dbList))
						$ips	=	array_merge($dbList,$ips);
				}
				else
					$ips	=	(!empty($dbList))? $dbList : false;
				
				return (is_array($ips))? $this->getRecursiveValues($ips) : false;
			}
		
		public	function onWhiteList($ip,$type = 'admintools')
			{
				# Get the whitelist
				$list	=	$this->getWhiteList($type);
				# If not there
				if(!is_array($list) || empty($list)) {
					# Just warn there is no listing
					$this->saveIncidental('whitelist_'.$type, array('whitelist_'.$type.'_warning'=>'no white list available for '.$type));
					# Return user allowed
					return true;
				}
				# If the value is returned but has a mixture of arrays and values
				if(isset($list[0]) && is_array($list[0])) {
					$new	=	array();
					# Loop through the list
					foreach($list as $ipSet) {
						# Filter values from arrays
						if(is_array($ipSet))
							$new	=	array_merge($ipSet,$new);
						else
							$new[]	=	$ipSet;
					}
					# Save to list value
					$list	=	$new;
				}
				
				return (in_array($ip,$list));
			}
		/*
		**	@description	This function pulls the data from the `file_types` table
		*/
		public	function getFileTypes()
			{
				if(empty($this->singleton['file_types']))
					$this->singleton['file_types']	=	$this->nGet()->getFileTypes();
				
				return $this->singleton['file_types'];
			}
		
		public	function getUploadDir($table,$settings = false)
			{
				$table		=	(!empty($table))? trim($table) : false;
				$append		=	(!isset($settings['append']) || !empty($settings['append']));
				$default	=	(!empty($settings['dir']))? $settings['dir'] : '/client/images/default/';
				
				if(empty($table))
					return $default;
				
				$dir	=	nquery()	->select("file_path")
										->from("upload_directory")
										->where(array("assoc_table"=>$table))
										->fetch();
				
				$path	=	($dir != 0)? $dir[0]["file_path"] : $default;
				
				return ($append)? str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.$path) : $path;
			}
		/*
		**	@description	This method will return the class object
		**	@param	$class	[string]	This is the class name
		**	@param	$inject	[~ANY~]	This will inject into the constructor
		**	@param	$dir	[string]	This will attempt to load the function first if the page found
		*/
		public	function getFunction($function,$inject = false,$dir = false)
			{
				# If function does not exist
				if(!function_exists($function)) {
					# See if there is a search-in folder, default is the native dir
					$search	=	(!empty($dir) && is_dir($dir))? $dir : NBR_FUNCTIONS;
					# Try and autoload the function
					$this->autoload($function, $search);
				}
				# If the function exists return it
				if(function_exists($function))
					return $function($inject);
				# If the force render is not on, throw an exception
				if(!$this->forceRender())
					throw new \Exception('Function is unavailable: '.self::call('Safe')->encode($function));
			}
		
		public	function getErrorMode($fromConfig = false)
			{
				return $this->getMode('error','E_ALL',$fromConfig);
			}
			
		public	function getServerMode($fromConfig = false)
			{
				return $this->getMode('server','production',$fromConfig);
			}
		
		public	function getMode($type,$default,$fromConfig = false)
			{
				$raw	=	strtolower($type.'_mode');
				$const	=	strtoupper($raw);
				
				if(!$fromConfig) {
					if(defined($const))
						return constant($const);
				}
				
				$mData	=	$this->getMatchedArray(array('ondefine',$raw));
				
				if(!empty($mData[$raw][0]) && !is_array($mData[$raw][0]))
					return $mData[$raw][0];
				
				return $default;
			}
		
		public	function getPrefFilePath($append = false)
			{
				$path	=	$this->getCacheFolder().DS.'prefs';
				if($append)
					$path	.=	DS.$append;
				
				return $path;
			}
		/*
		**	@description	Save a pref file
		*/
		public	function savePrefFile($name,$content,$type = 'json')
			{
				$path		=	$this->getPrefFilePath("{$name}.{$type}");
				$path		=	$this->toSingleDs($path);
				$content	=	($type == 'json')? json_encode($content) : $content;
				$this->saveFile($content,$path);
			}
			
		public	function deletePrefFile($name,$type = 'json')
			{	
				$path		=	$this->getCacheFolder().DS.'prefs'.DS."{$name}.{$type}";
				$path		=	$this->toSingleDs($path);
				if(is_file($path))
					unlink($path);
			}
		/*
		** @description	Saves a file to disk
		*/
		public	function saveFile($content,$path,$opts=false)
			{
				$data	=	array(
					'content'=>$content,
					'save_to'=>$path,
					'overwrite'=>(isset($opts['overwrite']))? $opts['overwrite'] : true,
					'secure'=>(isset($opts['secure']))? $opts['secure'] : true
				);
				
				$this->getHelper('nFileHandler')->writeToFile($data);
			}
		
		public	function getCachedPref($name,$type = 'json')
			{
				# Check if there is a cache pause on
				$allow	=	$this->nCache()->allowCacheRead();
				# Get the file path
				$file	=	$this->toSingleDs($this->getCacheFolder().DS.'prefs'.DS.$name.'.'.$type);
				if(!is_file($file) || !$allow)
					return false;
				$file	=	(file_get_contents($file));
				return ($type == 'json')? json_decode($type) : $file;
			}
		/*
		**	@description	Saves and fetches from the settings folder, not the cache folder.
		**	@param	$name [string]	Name of the file that is being saved/retrieved
		**	@param	$func	[function | any]	Callable function or data being saved to file
		**	@param	$ext	[string]	The file extension being save/extracted
		**	@param	$path	[string|bool(false)]	This is the path where the file will be saved from/to
		*/
		public	function getSettingsFile($name,$func,$ext = 'json',$path = false)
			{
				$ext	=	(empty($ext))? 'json' : $ext;
				$path	=	(!empty($path) && is_dir($path))? $path : NBR_CLIENT_SETTINGS.DS.'preferences';
				$file	=	$path.DS.$name.'.'.$ext;
				# Check if there is a cache pause on
				$allow	=	$this->nCache()->allowCacheRead();
				# Save htaccess file
				if($this->isDir($allow,false) && !is_file($htaccess = $path.DS.'.htaccess'))
					file_put_contents($htaccess,$this->getHelper('nReWriter')->getScript('serverReadWrite'));
				
				if(is_file($file) && $allow) {
					return ($ext == 'json')? json_decode(file_get_contents($file),true) : file_get_contents($file);
				}
				# Process
				$contents	=	(is_callable($func))? $func($this,$file) : $func;
				# Make save directory
				if(!$this->isDir($path))
					trigger_error("Path ({$path}) could not be saved for perminant storage.");
				if(is_array($contents) || is_object($contents))
					$contents	=	json_encode($contents);
				# Save file
				if(!$this->isAjaxRequest() && $allow)
					file_put_contents($file,$contents);
				# Return the contents for use
				return $contents;
			}
		
		public	function getPrefFile($name,$settings = false,$raw = false,$callback = false)
			{	
				$named			=	(!empty($settings['node']))? $settings['node'] : false;
				$pref_name		=	(!empty($settings['name']))? $settings['name'] : $name;
				$save			=	(!empty($settings['save']))? $settings['save'] : false;
				$parseLocation	=	(!empty($settings['xml']))? $settings['xml'] : false;
				$type			=	(!empty($settings['type']))? $settings['type'] : 'json';
				$matched		=	(!empty($settings['match']))? $settings['match'] : false;
				$limit			=	(!empty($settings['limit']))? $settings['limit'] : false;
				$pre_proc		=	(isset($settings['preprocess']))? $settings['preprocess'] : true;
				$reset			=	(isset($settings['reset']))? $settings['reset'] : true;
				$prefFile		=	($raw)? $name : $this->toSingleDs($this->getCacheFolder().DS.'prefs'.DS.$pref_name.'.'.$type);
				$Cache			=	$this->nCache();
				
				if(is_file($prefFile) && $Cache->allowCacheRead()) {
					$cont	=	json_decode(file_get_contents($prefFile),true);
					if($reset) {
						if(!empty($cont))
							return $cont;
					}
					else
						return $cont;
				}
				# If there is no directory set
				if(empty($parseLocation)) {
					if(is_callable($callback))
						$config	=	$callback($prefFile,$this);
					else
						return false;
				}
				else
					$parseFile		=	$this->toSingleDs($parseLocation.DS.$name.'.xml');
				
				if(!empty($parseFile) && is_file($parseFile)) {
					if(is_callable($callback)) {
						$config	=	$callback($parseFile,$this);
					}
					else {
						$config	=	$this->toArray($this->getHelper('nRegister')->parseXmlFile($parseFile));
						# If there is a matched array
						if(!empty($matched)) {
							# Retieve an array from the main array
							$matchArr	=	$this->getMatchedArray($matched,'_',$config);
							# Jump to the end of the search array
							end($matched);
							# Get the key
							$getLastKey	=	key($matched);
							# Get the last value from search array
							$lastKey	=	$matched[$getLastKey];
							# If there is a valid array do more to whittle it down
							if(!empty($matchArr[$lastKey])) {
								if($limit) {
									if($limit == 1) 
										$config	=	(isset($matchArr[$lastKey][0]))? $matchArr[$lastKey][0] : false;
									else {
										for($i = 0; $i < $limit; $i++)
											$config[$i]	=	$matchArr[$lastKey][$i];
									}
								}
								else
									$config	=	$matchArr[$lastKey];
							}
							else
								$config	=	false;
						}
					}
				}
				
				if(!isset($config))
					return array();
					
				if(is_array($config) && $pre_proc) {
					$nApp	=	$this;
					$config	=	$this->arrayWalkRecursive($config,function($value) use ($nApp) {
						$v	=	$nApp->getHelper('nAutomator',$this)->matchFunction($nApp->getBoolVal($value));
						return $v;
					});
				}
				
				if($save && !$this->isAjaxRequest() && $Cache->allowCacheRead() && !empty($config)) {
					$this->savePrefFile($pref_name,$config);
				}
				
				if($named) { // && !$this->isAjaxRequest() && $Cache->allowCacheRead()
					$this->saveSetting($named,$config);
				}
				
				return $config;
			}
		
		public	function getSiteTemplate()
			{
				if(!empty($this->getSitePrefs()->template_folder))
					return $this->getSitePrefs()->template_folder;
			}
		
		public	function getDefaultTemplate()
			{
				$pref		=	$this->toSingleDs(NBR_CLIENT_SETTINGS.DS.'template.pref');
				$default	=	DS.'core'.DS.'template'.DS.'default';
				if(is_file($pref))
					return (is_file($pref))? @file_get_contents($pref) : false;
				
				if(empty($this->getDataNode('preferences')))
					(new GetSitePrefs)->set();
				
				$getTemp	=	$this->getMatchedArray(array(
					'settings_site',
					'content',
					'template_folder'
				),'',$this->toArray($this->getDataNode('preferences')));

				$template	=	(!empty($getTemp['template'][0]))? trim($this->toSingleDs(DS.$getTemp['template'][0]),DS) : $pref;
				if(!is_dir(NBR_ROOT_DIR.DS.$template))
					$template	=	$default;
				
				$this->getHelper('nFileHandler')->writeToFile(array(
					'content'=>$template,
					'save_to'=>$pref,
					'overwrite'=>true,
					'secure'=>true
					));
				
				return $template;
			}
		
		public	function cacheHtml($type,$content)
			{
				$path	=	$this->getCacheFolder().DS.'html'.DS."{$type}.html";
				if($this->nCache()->allowCacheRead() && !$this->isAjaxRequest())
					$this->saveFile($content,$path);
			}
		
		public	function getCachedHtml($name)
			{
				$path	=	$this->getCacheFolder().DS.'html'.DS."{$name}.html";
				if(!is_file($path))
					return;
				
				return	$this->render($path);
			}
		/*
		**	@description	This overloading method will call a class based on the method name.
		**	@param	$name	[string]	This is automated by php and is the name of the method (and class)
		**	@param	$args	[bool|array]	This can pass arguments to the class being called
		**	@return	[object]	The class will return the class object
		*/
		public static	function __callStatic($name,$args = false)
			{
				return self::callClass($name,$args);
			}
		
		public	static	function call()
			{
				$args	=	func_get_args();
				$class	=	(!empty($args[0]))? $args[0] : false;
				$pass	=	(!empty($args[1]))? $args[1] : false;
				
				if(empty($class)) {
					if(!(self::$singleton instanceof \Nubersoft\nApp))
						self::$singleton	=	new nApp();
					
					return self::$singleton;
				}
				else {
					return self::callClass($class,false);
				}
			}
		/*
		public	function __call($name,$args=false)
			{
				return self::callClass($name,$args);
			}
		*/
		protected	static	function callClass($name,$args=false)
			{
				# If the method name has an underscore, try to namespace it
				if(strpos($name,'_') !== false) {
					# Trim it off the front and back
					$dynamic	=	trim($name,'_');
					# Namespace it
					$dynamic	=	str_replace('_','\\',$dynamic);
				}
				# Set the default name 
				$uName	=	(isset($dynamic))? '\\'.$dynamic : '\Nubersoft\\'.$name;
				# This is for a 1-inject object class
				if(count($args) == 1)
					$args	=	$args[0];
				# Try and return the class
				return (!empty($args))? self::getClass($uName,$args) : self::getClass($uName);
			}
		/*
		**	@description	This method will return the class object
		**	@param	$class	[string]	This is the class name
		**	@param	$inject	[~ANY~]	This will inject into the constructor
		*/
		public	static	function getClass($class,$inject = false)
			{
				return new $class($inject);
			}
		
		public	function getFrontEndPath($path)
			{
				return $this->getTemplatePathMatch($path);
			}
		
		public	function getBackEndPath($path)
			{
				return $this->getTemplatePathMatch($path,'backend');
			}
		
		public	function getTemplatePath($path,$strict = false)
			{
				return $this->getTemplatePathMatch($path,'dir');
			}
		
		public	function getTemplatePathMatch($path,$dirType = 'frontend',$array = false)
			{
				$templates	=	(!empty($array))? $array : $this->toArray($this->getDataNode('site')->templates);
				
				if(!is_array($templates))
					return;
				
				foreach($templates as $type) {
					if(is_file($file = NBR_ROOT_DIR.$type[$dirType].DS.$path))
						return $file;
					elseif(is_dir($dir = NBR_ROOT_DIR.$type[$dirType].DS.$path))
						return $dir;
				}
			}
		/*
		**	@description	Fetches current usergroup from the session
		*/
		public	function getCurrentGroup($type = true)
			{
				$getUser	=	$this->getSession('usergroup_data');
				
				if(empty($getUser))
					return false;
				
				return ($type)? $getUser->name : (int) $getUser->numeric;
			}
		
		public	function getUsergroup($usergroup=false)
			{
				return $this->convertUserGroup($usergroup);
			}
		
		public	function convertUserGroup($usergroup = false)
			{
				if(is_numeric($usergroup))
					return $usergroup;
				elseif(is_string($usergroup))
					return (defined($usergroup))? constant($usergroup) : $usergroup;
					
				$session	=	$this->getDataNode('_SESSION');
				$usergroup	=	(isset($session->usergroup))? $session->usergroup : false;
				
				if(empty($usergroup))
					return false;
				
				if(is_string($usergroup))
					return (defined($usergroup))? constant($usergroup) : $usergroup;
				
			}
			
		public	function removeDataNode($val)
			{
				$this->getHelper('NubeData')->clearNode($val);
			}
		/*
		**	@description	This will get the NodeData which is based on the NubeData class.
		**					Mainly meant to extract data from the array.
		*/
		public	function getNode($name)
			{
				return $this->getHelper('NodeData')->getNode($name);
			}
		
		public	function checkEmptyResponse($value,$ajax = 'Action failed',$default = false)
			{
				if(empty($value)) {
					if(!$this->isAjaxRequest())
						return $default;
					
					$array	=	(is_array($ajax))? $ajax : array('success'=>false,'alert'=>$ajax);
					
					die(json_encode($array));
				}
				
				return true;
			}
		
		public	function setSession($var,$value,$reset = true)
			{
				$this->getHelper('nSessioner')->setSession($var,$value,$reset);
			}
		
		public	function getAllAlerts($key = false)
			{
				$errors				=
				$warnings			=	array();
				$err				=	$this->toArray($this->getError());
				$inc				=	$this->toArray($this->getIncidental());
				
				if(!empty($err))
					$this->flattenArrayByKey($err,$errors,'msg');
				
				if(!empty($inc))
					$this->flattenArrayByKey($inc,$warnings,'msg');
				
				$array['warnings']	=	$warnings;
				$array['errors']	=	$errors;
				
				return (!empty($key) && isset($array[$key]))? $array[$key] : $array;
			}
			
		public	function getAlertsByKind($key = false,$type = false)
			{
				$errors				=
				$warnings			=	array();
				$err				=	$this->toArray($this->getError());
				$inc				=	$this->toArray($this->getIncidental());
				
				if(!empty($err) && isset($err[$key]))
					$this->flattenArrayByKey($err,$errors,'msg');
				
				if(!empty($inc) && isset($inc[$key]))
					$this->flattenArrayByKey($inc,$warnings,'msg');
				
				$array['warnings']	=	$warnings;
				$array['errors']	=	$errors;
			
				if(!empty($type))
					return (isset($array[$type]))? $array[$type] : array();
					
				return $array;
			}
		/*
		**	@description	Creates a autoloader for classes
		**	@param	$path	[string | anon func]	This can be a path where classes can be found OR<br>
		**					a callable function that the spl uses to create a loader
		*/
		public	function addNamespace($path)
			{
				$nApp	=	$this;
				
				if(is_callable($path))
					spl_autoload_register($path);
				else {
					spl_autoload_register(function($class) use ($path,$nApp) {
						
						if(is_array($path)) {
							foreach($path as $namespace) {
								$classPath	=	$nApp->toSingleDs($namespace.DS.str_replace('\\',DS,$class)).'.php';
						
								if(is_file($classPath))
									require_once($classPath);
								}
						}
						else {
							$classPath	=	$nApp->toSingleDs($path.DS.str_replace('\\',DS,$class)).'.php';
						
							if(is_file($classPath))
								require_once($classPath);
						}
					});
				}
				return $this;
			}
		/*
		**	@description	Creates a standard cache path for saving cached elements into the cache folder
		**	@param	$appendPath	[string|empty]	Self-explanitory
		**	@param	$cou	[string]	This is the default locale
		**	@param	$func	[anon func|empty]	This can be a callable function to process a new cache path
		*/
		public	function getStandardPath($appendPath = false,$cou = 'USA',$func = false)
			{
				$cacheDir	=	$this->getCacheFolder().DS.'pages'.DS;
				$state		=	(empty($this->getPageURI('is_admin')) || $this->getPageURI('is_admin') > 1)? 'base_view' : 'admin_view';
				$toggled	=	(!empty($this->getDataNode('_SESSION')->toggle->edit))? 'is_toggled' : 'not_toggle';
				$post		=	$this->getPost('action');
				$get		=	$this->getGet('action');
				$usePost	=	(is_string($post))? DS.md5($post) : '';
				$useGet		=	(is_string($get))? DS.md5($get) : '';
				$country	=	(!empty($this->getSession('LOCALE')))? trim($this->getSession('LOCALE'),'/') : $cou;
				$tempBase	=	$this->getDataNode('site')->templates->template_site->dir;
				$defPath	=	(!empty($this->getPageURI('full_path')))? trim(str_replace('/',DS,$this->getPageURI('full_path')),DS) : 'static';
				$ID			=	(!empty($this->getPageURI('ID')))? $this->getPageURI('ID') : (($defPath == 'static')? 'error' : md5($defPath));
				$loggedIn	=	($this->isLoggedIn())? 'loggedin' : 'loggedout';
				$usergroup	=	(!empty($this->getSession('usergroup')))? $this->getSession('usergroup') : 'static';
				$isSsl		=	($this->isSsl())? 'https' : 'http';
				$group_id	=	(!empty($this->getSession('group_id')))? 'gid_'.$this->getSession('group_id') : 'gid_base';
				$finalPath	=	$this->toSingleDs($cacheDir.DS.$country.DS.$isSsl.DS.$tempBase.DS.$defPath.DS.$loggedIn.$useGet.$usePost.DS.$toggled.DS.$state.DS.$usergroup.DS.$group_id.DS.$ID.$appendPath);
				
				if(is_callable($func))
					return $func($this,$finalPath);
				else
					return $finalPath;
				
			}
		/*
		**	@description	Alias to the nRouter method to create an OpenSSL encoded string containing path
		*/
		public	function setJumpPage($url,$urlencode = true)
			{
				return $this->getHelper('nRouter')->createJumpPage($url,$urlencode);
			}
		/*
		**	@description	Alias to the nRouter method to decode the OpenSSL-encoded string containing path
		*/
		public	function getJumpPage($url)
			{
				return $this->getHelper('nRouter')->decodeJumpPage($url);
			}
		
		public	function mask($string)
			{
				if(is_array($string) || is_object($string))
					$string	=	json_encode($string);
					
				return $this->safe()->encOpenSSL($string);
			}
		
		public	function unmask($string,$urlencode = false,$base64=false)
			{
				return $this->safe()->decOpenSSL($string,array('urlencode'=>$urlencode,'base64'=>$base64));
			}
		
		public	function getLocale($default = 'USA')
			{
				$locale	=	$this->getSession('LOCALE');
				return (!empty($locale))? strtoupper(trim($locale,'/')) : $default;
			}
		/*
		**	@description	Alias to fetch the Cart Object
		*/
		public	function getCart($type = '\Model')
			{
				if(is_array($type))
					$type	=	'\\'.implode('\\',$type);
					
				return $this->getPlugin('\nPlugins\Nubersoft\ShoppingCart'.$type);
			}
		/*
		**	@description	Alias to fetch the Messenger Object
		*/
		public	function getMessenger()
			{
				return $this->getHelper('Messenger');
			}
		/*
		**	@description	Alias to fetch the cache engine
		*/
		public	function nCache()
			{
				return $this->getPlugin('\nPlugins\Nubersoft\Cache');
			}
		/*
		**	@description	Alias to fetch the Data storage engine
		*/
		public	function getData()
			{
				return new NubeData();
			}
	}