<?php
/**
*	Copyright (c) 2017 Nubersoft.com
*	Permission is hereby granted, free of charge *(see acception below in reference to
*	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
*	and associated documentation files (the "Software"), to deal in the Software without
*	restriction, including without limitation the rights to use, copy, modify, merge, publish,
*	or distribute copies of the Software, and to permit persons to whom the Software is
*	furnished to do so, subject to the following conditions:
*	
*	The base CMS software* is not used for commercial sales except with expressed permission.
*	A licensing fee or waiver is required to run software in a commercial setting using
*	the base CMS software.
*	
*	*Base CMS software is defined as running the default software package as found in this
*	repository in the index.php page. This includes use of any of the nAutomator with the
*	default/modified/exended xml versions workflow/blockflows/actions.
*	
*	The above copyright notice and this permission notice shall be included in all
*	copies or substantial portions of the Software.
*
*	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*	SOFTWARE.
*	*SNIPPETS:*
*	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
*	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
*	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;
/**
		*	This will enable the nGet class to retrieve elements from the NubeData object as well as return any number
* 	of important elements. If a method is not in this class, it will be called from __callStatic()
*/
class	nApp extends \Nubersoft\nFunctions
{
	protected	static	$site_info;
	protected	$Cache;
	/**
	*	@description	Trims down the url of leading and trailing forward slashes
	*/
	private	function trimUrl($name,$url)
	{
		$url	=	(!empty($url))? '/'.trim($url,'/') : '';
		return '/'.$name.$url;
	}
	/**
	*	@description	Fetches the media folder path including http
	*/
	public	function mediaUrl($url = false,$force = false)
	{
		return $this->siteUrl($this->trimUrl('media',$url),array('locale'=>false),$force);
	}
	/**
	*	@description	Fetches the images folder path including http
	*/
	public	function imagesUrl($url = false,$force = false)
	{
		return $this->mediaUrl($this->trimUrl('images',$url),$force);
	}
	/**
	*	@description	Fetches the css folder path including http
	*/
	public	function cssUrl($url = false,$force = false)
	{
		return $this->mediaUrl($this->trimUrl('css',$url),$force);
	}
	/**
	*	@description	Fetches the js folder path including http
	*/
	public	function jsUrl($url = false,$force = false)
	{
		return $this->mediaUrl($this->trimUrl('js',$url),$force);
	}
	/**
	*	@description	Fetches the url including the locale
	*/
	public	function localeUrl($url = false,$force = false)
	{
		return $this->siteUrl($url,$force);
	}
	/**
	*	@description	Fetches the url inside the client folder
	*/
	public	function clientUrl($url,$force=false)
	{
		return $this->siteUrl('/client'.$url,$force);
	}
	/**
	*	@description	Fetches the url inside the client/media folder
	*/
	public	function cMediaUrl($url,$force=false)
	{
		return $this->clientUrl('/media'.$url,$force);
	}
	/**
	*	@description	Fetches the url inside the client/media/images folder
	*/
	public	function cImagesUrl($url,$force=false)
	{
		return $this->cMediaUrl('/images'.$url,$force);
	}
	/**
	*	@description	Fetches the url inside the client/media/css folder
	*/
	public	function cCssUrl($url,$force=false)
	{
		return $this->cMediaUrl('/css'.$url,$force);
	}
	/**
	*	@description	Fetches the url inside the client/media/js folder
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
	/**
	*	@description	Fetches the site url
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
		$baseUrl	=	(defined("BASE_URL") && BASE_URL != '{domain}')? BASE_URL : 'http://'.$_SERVER['HTTP_HOST'];
		$sslUrl		=	(defined("BASE_URL_SSL") && BASE_URL_SSL != '{domain}')? BASE_URL_SSL : 'https://'.$_SERVER['HTTP_HOST'];
		$forceSSL	=	(defined("FORCE_URL_SSL"))? FORCE_URL_SSL : $forceSSL;

		if($forceSSL)
			return $sslUrl.$url;
		else
			return ($this->isSsl())? $sslUrl.$url : $baseUrl.$url;
	}
	/**
	*	@description	Returns the parsed url but can retreive any parse_url() type found at:
	*					http://php.net/manual/en/function.parse-url.php
	*/
	public	function siteHost($settings = false)
	{
		$settings	=	(!empty($settings))? $settings : PHP_URL_HOST;
		return parse_url($this->localeUrl(),$settings);
	}
	/**
	*	@description	Multi-domain comparison
	*/
	public	function isSiteHost($site,$parse=true)
	{
		if($parse)
			$site	=	parse_url($site,PHP_URL_HOST);

		return ($this->siteHost() == $site);
	}
	/**
	*	@description	This will retrieve any stored data in the NubeData data object
	*	@param	$key	[string]	This will return a first-layer node from the data object
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
	/**
	*	@description	Sets a public data node to dyanamic node (Methodize)
	*	@param	$key	[string]	Value from the data node to set to methodize
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
	/**
	*	@description	This will check in the NubeData data object to see if it's available
	*/
	public	function issetDataNode($key)
	{
		# Use nGet to retrieve an element from data array
		return $this->nGet()->issetCoreElement($key);
	}
	/**
	*	@description	This will check in the NubeData data object to see if it's available
	*/
	public	function deleteDataNode($key)
	{
		# Use nGet to retrieve an element from data array
		if($this->nGet()->issetCoreElement($key)){
			unset(parent::$settings->{$key});
		}

		return ($this->nGet()->issetCoreElement($key));
	}
	/**
	*	@description	This will override first-level NubeData data objects
	*/
	public	function resetDataNodeVals($array)
	{
		foreach($array as $key => $values) {
			foreach($values as $skey => $sval) {
				parent::$settings->{$key}->{$skey}	=	(is_array($sval))? $this->toObject($sval) : $sval;
			}
		}
	}
	/**
	*	@description	Fetches the server version of the $_SESSION
	*/
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
	/**
	*	@description	Get sessionized user id
	*/
	public	function getUserId()
	{
		return $this->getSession('ID');
	}
	/**
	*	@description	Get the status of the database connection
	*/
	private static	function getCore($key = false)
	{
		return $this->getDataNode($key);	
	}
	/**
	*	@description	Get the status of the database connection
	*/
	public	function getConStatus()
	{
		$con	=	$this->nQuery()->getConnection();
		return (!empty($con));
	}

	/**
	*	@description	Fetches the site logo
	*/
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
	/**
	*	@description	Fetch the template engine
	*	@return			nTemplate class
	*/
	public	function fetchTemplate()
	{
			return $this->getHelper('nTemplate');
	}
	/**
	*	@description	Fetch value from the requested global
	*/
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
	/**
	*	@description	Sanitizes input and saves it to data nodes
	*/
	private	function getGlobal($key = false)
	{
		$key	=	strtoupper("_{$key}");

		if(!isset(parent::$settings->{$key}))
			$this->getHelper('Submits')->sanitize();

		if(!empty($key) && empty($this->getDataNode($key)))
			return false;

		if(!empty($this->getDataNode($key)))
			return $this->getDataNode($key);

		return false;
	}
	/**
	*	@description	Fetch value from the RAW $_POST array (or the full array if key blank)
	*/
	public	function getRawPost($key = false)
	{
		return $this->getGlobalArr('raw_post',$key);
	}
	/**
	*	@description	Fetch value from the RAW $_GET array (or the full array if key blank)
	*/
	public	function getRawGet($key = false)
	{
		return $this->getGlobalArr('raw_get',$key);
	}
	/**
	*	@description	Fetch value from the $_POST array (or the full array if key blank)
	*/
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
	/**
	*	@description	Fetch value from the $_GET array (or the full array if key blank)
	*/
	public	function getGet($key = false)
	{
		return $this->getGlobalArr('get',$key);
	}
	/**
	*	@description	Node version of getSession()
	*/
	public	function getSessionNode()
	{
		$SESSION	=	$this->toArray($this->getDataNode('_SESSION'));
		$Methodize	=	new Methodize();
		$Methodize->saveAttr('session',$SESSION);
		return $Methodize->session();
	}
	/**
	*	@description	Fetches the session array (or value)
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
				if($remove) {
					if(is_array($key))
						trigger_error('You can not remove session values in bulk.',E_USER_NOTICE);
					else {
						$this->getHelper('nSessioner')->destroy($key);
					}
				}
				
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
	/**
	*	@description	Saves errors to session and errors or incidentals data node
	*/
	public	function toAlert($msg, $action = 'general', $opts = false, $type = true, $toSess = true)
	{
		$this->getHelper('Messenger')->{__FUNCTION__}($msg, $action, $opts, $type, $toSess);
	}
	/**
	*	@description	Saves errors to session and errors or incidentals data node
	*/
	public	function toError($msg, $action = 'general', $opts = false, $toSess = true)
	{
		$this->toAlert($msg, $action, $opts, false, $toSess);
	}
	/**
	*	@description	Retrieves errors from session and errors or incidentals data node
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
	/**
	*	@description	Fetch messages from the \Nubersoft\DataNode::$errors array
	*/
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
	/**
	*	@description	Checks if usergroup is an admin (alias of isAdmin())
	*/
	public	function adminCheck($usergroup = false)
	{
		if(empty($usergroup))
			return false;

		return $this->isAdmin($usergroup);
	}
	/**
	*	@description	Checks if the current page is has a cache status
	*/
	public	function getCachedStatus()
	{
		return $this->nGet()->getPage("auto_cache");
	}
	/**
	*	@description	Checks if the user is logged in but not admin
	*/
	public	function loggedInNotAdmin($username = false)
	{
		return $this->getHelper('UserEngine')->isLoggedInNotAdmin($username);
	}
	/**
	*	@description	Checks if user is loggedin
	*/
	public	function isLoggedIn($username = false)
	{
		return $this->getHelper('UserEngine')->isLoggedIn($username);
	}
	/**
	*	@description	Fetches the current user's info
	*/
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
	/**
	*	@description	Saves a log file
	*/
	public	function saveToLogFile($filename = false,$message = false,$opts = false)
	{
		$this->getHelper('nLogger')->saveToLog($filename,$message,$opts);
	}
	/**
	*	@description	Fetches the page data data by ID
	*/
	public	function getPageById($id,$key=false)
	{
		return $this->getHelper('nRouter\Model')->{__FUNCTION__}($id,$key);
	}
	/**
	*	@description Fetches page data	
	*/
	public	function getPage()
	{
		$args		=	func_get_args();
		$var		=	(!empty($args[0]))? $args[0] : false;
		return $this->getHelper('nRouter\Model')->{__FUNCTION__}($var);
	}
	/**
	*	@description	Fetches current template info
	*/
	public	function getTemplate()
	{
		return $this->getSite('template_current');
	}
	/**
	*	@description	Fetches the site prefs
	*/
	public	function getSite($var = false)
	{
		$this->getHelper('GetSitePrefs')->setPageRequestSettings();
		return $this->nGet()->{"getSite"}($var);
	}
	/**
	*	@description	Fetches the current user data (or attribute from)
	*/
	public	function getUser()
	{
		$args	=	func_get_args();
		$var	=	(!empty($args[0]))? $args[0] : false;
		$user	=	$this->getDataNode('user');

		if(empty($user))
			return false;

		if(!empty($var))
			return (!empty($user->{$var}))? $user->{$var} : false;
		else
			return $user;
	}
	/**
	*	@description	Fetches the User class
	*	@return			\Nubersoft\User class
	*/
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
	/**
	*	@description	Fetches the database table name
	*/
	public	function getDbName()
	{
		return (new \Nubersoft\FetchCreds())->getData();
	}
	/**
	*	@description	Fetches the current head prefs
	*/
	public	function getHead()
	{
		return $this->getPreferences('head');
	}
	/**
	*	@description	Fetches a the head prefs (or attribute from)
	*/
	public	function getHeader($var = false)
	{
		$head	=	$this->getHead();
		if(empty($head))
			return false;

		if(!empty($var))
			return (isset($head->{$var}))? $head->{$var} : false;

		return $head;
	}
	/**
	*	@description	Specifically fetches the content from the head prefs
	*/
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
	/**
	*	@description	Fetches the request preferences
	*/
	public	function getPreferences($val = 'site')
	{
		$prefs	=	$this->getDataNode('preferences');
		$array	=	(isset($prefs->{"settings_{$val}"}))? $prefs->{"settings_{$val}"} : false;
		if(!empty($array)) {
			return (!empty($array))? $array : false;
	}
	}
	/**
	*	@description	Get the footer preferences
	*/
	public	function getFooter()
	{
		return $this->getPreferences('foot');
	}
	/**
	*	@description	Specifically get the footer content
	*/
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
	/**
	*	@description	Fetche the favicon data from the footer prefs
	*/
	public	function getFavicons($var = false)
	{
		return self::getClass('\Nubersoft\Safe')->decode($this->getHeaderContent('favicons'));
	}
	/**
	*	@description	Fetches the javascript content from the header prefs
	*/
	public	function getJavascript($var = false)
	{
		return self::getClass('\Nubersoft\Safe')->decode($this->getHeaderContent('javascript'));
	}
	/**
	*	@description	Fetches the salt from the prefs
	*/
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
	/**
	*	@description	Fetches the current table being accessed
	*/
	public	function getTableName()
	{
		$engine	=	$this->getDataNode();
		if(!empty($engine->table_name))
			return $engine->table_name;

		return 'users';
	}
	/**
	*	@description	Creates the site preferences
	*/
	protected	function createPrefs()
	{
		self::call("GetSitePrefs")->set();
	}
	/**
	*	@description	Fetches the content from any of the preferences
	*/
	public	function getPrefsContent($type = 'site')
	{
		$prefs	=	$this->getPreferences($type);
		return (!empty($prefs->content))? $prefs->content : false;
	}
	/**
	*	@description	Fetches the site preferences (content)
	*/
	public	function getSitePrefs($refresh = false)
	{
		if(!empty($this->getPrefsContent('site')))
			$site	=	$this->getPrefsContent('site');
		else {
			# Creates the prefs first
			$this->createPrefs();
			$site	=	$this->getPrefsContent('site');
		}

		return $this->toObject($site);
	}
	/**
	*	@description	Retrieve the reg file specifically
	*/
	public	function getRegistry($key = false)
	{
		return $this->settingsManager()->{__FUNCTION__}($key);
	}
	/**
	*	@description	This method gets the paths to search out for new config files
	*	@param	[object] This will be an instance of a parser class (nRegister() is default)
	*/
	public	function getLoadZones($xmlParser = false)
	{
		return $this->settingsManager()->{__FUNCTION__}($xmlParser);
	}
	/**
	*	@description	Fetches the stored configs file (xml-based)
	*/
	public	function getConfigs()
	{
		$args		=	func_get_args();
		$location	=	(!empty($args[0]))? $args[0] : false;
		return $this->settingsManager()->{__FUNCTION__}($location);
	}	
	/**
	*	@description	
	*/
	public	function getPlugins()
	{
		if(!empty($this->singleton['getPlugins']))
			return $this->singleton['getPlugins'];

		$plugin	=	$this->getDataNode('plugin');

		if(!empty($plugin))
			return $this->singleton['getPlugins']	=	$plugin;

		return $this->singleton['getPlugins'] = false;
	}
	/**
	*	@description	Main return for the database connection
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
	/**
	*	@description	Fetches the path for the database credentials
	*/
	public	function getDbCredsFile()
	{
		$path	=	NBR_CLIENT_DIR.DS.'settings'.DS.'dbcreds.php';
		return (is_file($path))? $path : false;
	}
	/**
	*	@description	Fetches (and sets if not set) the database tables
	*/
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
	/**
	*	@description	Checks if the user, either by value passed or by session, is an admin
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
	/**
	*	@description	Matches current usergroup
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
	/**
	*	@description	Fetches my usergroup
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
	/**
	*	@description	Checks if a database table is valid
	*/
	public	function tableValid($table = false)
	{
		if(empty($table))
			return false;
		$getTables	=	$this->getTables();
		if(empty($getTables) || (!empty($getTables) && !is_array($getTables)))
			return false;

		return in_array($table,$this->getTables());
	}
	/**
	*	@description	Checks if the site is live or not
	*/
	public	function siteLive()
	{
		$site	=	$this->getSitePrefs();
		return	(!empty($site->site_live->toggle) && $site->site_live->toggle == 'on');
	}
	/**
	*	@description	Checks from the content if the site is live or not
	*/
	public	function siteLiveStatus()
	{
		$site	=	$this->getSitePrefs();
		return	(isset($site->content->site_live->toggle) && $site->content->site_live->toggle == 'on');
	}
	/**
	*	@description	Implimentation/Alias of getConStatus()
	*/
	public	function siteValid()
	{
		return	$this->getConStatus();
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
		if(isset(parent::$settings->admin_page)) {
			if(!empty($key))
				return (isset(parent::$settings->admin_page->{$key}))? parent::$settings->admin_page->{$key} : false;
			else
				return	parent::$settings->admin_page;
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
	/**
	*	@description	Fetches the current page
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
		parent::$settings->engine->table		=	$table;
		parent::$settings->engine->table_name	=	$table;
	}
	/**
	*	@description	Returns the designated settings folder
	*/
	public	function getSettingsDir($append = false)
	{
		return (!empty($append))? $this->toSingleDs(NBR_CLIENT_SETTINGS.DS.$append) : NBR_CLIENT_SETTINGS;
	}
	/**
	*	@description	Returns the designated cache folder
	*/
	public	function getCacheFolder($append = false)
	{
		return $this->getHelper('nCache')->{__FUNCTION__}($append);
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
	/**
	*	@description	Check if there is a login restriction
	*/
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
	/**
	*	@description	This method just checks if the force render is set.
	*					This will allow the page to keep rendering with defaults if true
	*/
	public	function forceRender()
	{
		return $this->getMode('force',true);
	}
	/**
	* @description	Returns a php to javascript library
	*/
	public	function jsEngine()
	{
		return new \nPlugins\Nubersoft\JsLibrary();
	}

	public	function getEmailer()
	{
		return $this->getHelper('Emailer');
	}

	public	function cacheEngine()
	{
		return $this->getHelper('BuildCache');
	}

	public	function userCount()
	{
		$nGet	=	new nGet();
		return $nGet->getUserCount();
	}

	public	function nFunc()
	{
		return $this;
	}

	public	function nSession()
	{
		return $this->getHelper('nSessioner');
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
	/**
	*	@description	Searches config for a whitelist
	*	@use			<whitelist>
	*						<admintools>
	*							<ip>12.123.12.123</ip>
	*						</admintools>
	*					</whitelist>
	*/
	public	function getWhiteList($type)
	{
		return $this->getPlugin('\nPlugins\Nubersoft\Permission')->{__FUNCTION__}($type);
	}
	/**
	*	@description	Checks if element is in a whitelist
	*/
	public	function onWhiteList($ip,$type = 'admintools')
	{
		return $this->getPlugin('\nPlugins\Nubersoft\Permission')->{__FUNCTION__}($ip,$type);
	}
	/**
	*	@description	This function pulls the data from the `file_types` table
	*/
	public	function getFileTypes()
	{
		if(empty($this->singleton['file_types']))
			$this->singleton['file_types']	=	$this->nGet()->getFileTypes();

		return $this->singleton['file_types'];
	}

	public	function getUploadDir($table,$settings = false)
	{
		return $this->fileManager('Controller')->{__FUNCTION__}($table,$settings);
	}
	/**
	*	@description	This method will return the class object
	*	@param	$class	[string]	This is the class name
	*	@param	$inject	[~ANY~]	This will inject into the constructor
	*	@param	$dir	[string]	This will attempt to load the function first if the page found
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
			throw new \Exception('Function is unavailable: '.$this->safe()->encode($function));
	}

	public	function getPrefFilePath($append = false)
	{
		$path	=	$this->getCacheFolder().DS.'prefs';
		if($append)
			$path	.=	DS.$append;

		return $path;
	}
	/**
	*	@description	Save a pref file
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
	/**
	* @description	Saves a file to disk
	*/
	public	function saveFile($content,$path,$opts=false)
	{
		$data	=	array(
			'content'=>$content,
			'save_to'=>$path,
			'overwrite'=>(isset($opts['overwrite']))? $opts['overwrite'] : true,
			'secure'=>(isset($opts['secure']))? $opts['secure'] : true
		);

		$this->fileManager()->writeToFile($data);
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
	/**
	*	@description	Saves and fetches from the settings folder, not the cache folder.
	*	@param	$name [string]	Name of the file that is being saved/retrieved
	*	@param	$func	[function | any]	Callable function or data being saved to file
	*	@param	$ext	[string]	The file extension being save/extracted
	*	@param	$path	[string|bool(false)]	This is the path where the file will be saved from/to
	*/
	public	function getSettingsFile($name,$func,$ext = 'json',$path = false)
	{
		return $this->fileManager('Controller')->{__FUNCTION__}($name,$func,$ext,$path);
	}

	public	function getPrefFile($name,$settings = false,$raw = false,$callback = false)
	{
		return $this->fileManager('Controller')->{__FUNCTION__}($name,$settings,$raw,$callback);
	}
	/**
	*	@description	Alias/wrapper to the nFileHandler
	*/
	public	function fileManager($append='')
	{
		return $this->getHelper(trim('nFileHandler\\'.$append,'\\'));
	}

	public	function getSiteTemplate()
	{
		if(!empty($this->getSitePrefs()->template_folder))
			return $this->getSitePrefs()->template_folder;
	}

	public	function getDefaultTemplate()
	{
		return $this->getHelper('nTemplate')->{__FUNCTION__}();
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
	/**
	*	@description	This method will return the class object
	*	@param	$class	[string]	This is the class name
	*	@param	$inject	[~ANY~]	This will inject into the constructor
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
		return $this->getHelper('nTemplate')->{__FUNCTION__}($path,$dirType,$array);
	}
	/**
	*	@description	Fetches current usergroup from the session
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
	/**
	*	@description	This will get the NodeData which is based on the NubeData class.
	*					Mainly meant to extract data from the array.
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
	/**
	*	@description	Set a session variable (also sets a data node)
	*/
	public	function setSession($var,$value,$reset = true)
	{
		$this->getHelper('nSessioner')->setSession($var,$value,$reset);
	}
	/**
	*	@description	Creates a autoloader for classes
	*	@param	$path	[string | anon func]	This can be a path where classes can be found OR<br>
	*					a callable function that the spl uses to create a loader
	*/
	public	function addNamespace($path)
	{
		return $this->getHelper('nRouter\Controller')->addNamespace($path);
	}
	/**
	*	@description	Creates a standard cache path for saving cached elements into the cache folder
	*	@param	$appendPath	[string|empty]	Self-explanitory
	*	@param	$cou	[string]	This is the default locale
	*	@param	$func	[anon func|empty]	This can be a callable function to process a new cache path
	*/
	public	function getStandardPath($appendPath = false,$cou = 'USA',$func = false)
	{
		return $this->getHelper('nCache')->{__FUNCTION__}($appendPath,$cou,$func);
	}
	/**
	*	@description	Alias to the nRouter method to create an OpenSSL encoded string containing path
	*/
	public	function setJumpPage($url,$urlencode = true)
	{
		return $this->getHelper('nRouter')->createJumpPage($url,$urlencode);
	}
	/**
	*	@description	Alias to the nRouter method to decode the OpenSSL-encoded string containing path
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
	/**
	*	@description	Alias to fetch the Cart Object
	*/
	public	function getCart($type = '\Model')
	{
		if(is_array($type))
			$type	=	'\\'.implode('\\',$type);

		return $this->getPlugin('\nPlugins\Nubersoft\ShoppingCart'.$type);
	}
	/**
	*	@description	Alias to fetch the Messenger Object
	*/
	public	function getMessenger()
	{
		return $this->getHelper('Messenger');
	}
	/**
	*	@description	Alias to fetch the cache engine
	*/
	public	function nCache()
	{
		return $this->getPlugin('\nPlugins\Nubersoft\Cache');
	}
	/**
	*	@description	Alias to fetch the Data storage engine
	*/
	public	function getData()
	{
		return new NubeData();
	}
	/**
	*	@description	Alias to fetch the settings controller
	*/
	public	function settingsManager()
	{
		return $this->getPlugin('\nPlugins\Nubersoft\Settings\Controller');
	}
	/**
	*	@description	Static overlader, default is to call nApp (itself). Wrapper to callClass() and return self
	*	@param	$arg1	[string]	The name of the method (and class)
	*	@param	$arg2	[any]		This can pass arguments to the class being called
	*/
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
	/**
	*	@description	Wrapper/Alias for getting the current error mode
	*/
	public	function getErrorMode($fromConfig = false)
	{
		return $this->settingsManager()->getErrorMode($fromConfig);
	}
	/**
	*	@description	Wrapper/Alias for getting the current server mode
	*/
	public	function getServerMode($def = 'live',$fromConfig = false)
	{
		return $this->settingsManager()->getServerMode($def,$fromConfig);
	}
	/**
	*	@description	Fetches modes from the user config or registry if set
	*	@returns		Returns either the contstant's value or default value ($default)
	*/
	public	function getMode($type,$default,$fromConfig = false)
	{
		return $this->settingsManager()->getMode($type,$default,$fromConfig);
	}
	/**
	*	@description	Out of scope retrieval files
	*	@returns		Returns content
	*/
	public	function getDefaultIncludes($folder,$content=false,$corepath = 'plugins',$filename = 'index.php')
	{
		if(!defined('NBR_CLIENT_TEMPLATES') || !defined('NBR_TEMPLATE_DIR')) {
			trigger_error('You are calling this method too early, required defines are not yet set.',E_USER_NOTICE);
			return false;
		}
		
		$base	=	rtrim($corepath,DS).DS.$folder.DS.ltrim($filename,DS);
		$client	=	NBR_CLIENT_TEMPLATES.DS.$base;
		$core	=	NBR_TEMPLATE_DIR.DS.'default'.DS.$base;
		
		ob_start();
		if(is_file($client))
			include($client);
		else
			include($core);
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
	/**
	*	@description	Return the $_FILES array
	*/
	public	function getFiles($raw=false)
	{
		return (!empty($raw))? $_FILES : $this->getDataNode('_FILES');
	}
	/**
	*	@description	Used to decode html entities
	*/
	public	function decode($content)
	{
		return $this->getHelper('Safe')->decode($content);
	}
	/**
	*	@description	Used to encode html entities
	*/
	public	function encode($content)
	{
		return $this->getHelper('Safe')->encode($content);
	}
	/**
	*	@description	Retrieve error messages
	*/
	public	function getAllAlerts($key = false)
	{
		return $this->getHelper('Messenger')->getAllAlerts($key);
	}
	/**
	*	@description	Retrieve error messages
	*/
	public	function getAlertsByKind($key = false,$type = false)
	{
		return $this->getHelper('Messenger')->getAlertsByKind($key,$type);
	}
	
	public	function doMessageService($name,$args=false)
	{
		$nAlert		=	$this->getHelper('nAlert');
		$msgType	=	$nAlert->sortMessageTypes($name);
		$kind		=	$msgType['type'];
		$type		=	(isset($msgType['admin']))? 'admin' : 'general';
		$persist	=	(isset($msgType['persist']));
		
		if(count($args) > 1 && !empty($args[1]))
			$type	=	$args[1];
		
		if(isset($msgType['to'])) {
			switch($kind){
				case('alert'):
					$meth	=	"saveAlert";
					break;
				case('error'):
					$meth	=	"saveError";
					break;
				default:
					$meth	=	"saveSuccess";
			}
			
			return $nAlert->{$meth}($args[0],$type,$persist);
		}
		else {
			
			if(!empty($args[0]))
				$type	=	$args[0];
			
			if(!$persist) {
				switch($kind){
					case('alert'):
						$meth	=	"getAlert";
						break;
					case('error'):
						$meth	=	"getError";
						break;
					default:
						$meth	=	"getSuccess";
						break;
				}
				
				return $nAlert->{$meth}($type);
			}
			else
				return $nAlert->getStoredMessage($kind,$type);
		}
	}
	
	public	function getSystemMessages($kind='',$clear=false)
	{
		return $this->getHelper('nAlert')->{__FUNCTION__}($kind,$clear);
	}
	/**
	*	@description	This overloading method will call a class based on the method name.
	*	@param	$name	[string]	This is automated by php and is the name of the method (and class)
	*	@param	$args	[bool|array]	This can pass arguments to the class being called
	*	@return	[object]	The class will return the class object
	*/
	public static	function __callStatic($name,$args = false)
	{
		return self::callClass($name,$args);
	}
	
	public	function __call($name,$args=false)
	{
		# Process messaging
		if(stripos($name,'msg') !== false) {
			return $this->doMessageService($name,$args);
		}
		else {
			$errmsg['unknown']	=	'"'.$name.'" is invalid.';
			$errmsg['func']		=	'You are accessing a function dynamically as a last resort. Don\'t do this!';
			$errmsg['class']	=	'You are accessing a class dynamically as a last resort. It may be faster use ';
			$errmsg['data']		=	'You are accessing data as a last resort. It may speed up the action by using ';
			$revName	=	str_replace('_','\\',$name);
			# If calling "$this->Namespace_Class_Name()" it will convert to PSR-4
			if(class_exists($revName)) {
				trigger_error($errmsg['class'].'"new \\'.$revName.'()".',E_USER_NOTICE);
				return new $revName($args);
			}
			# Check if the class is exactly as requested: $this->className()
			elseif(class_exists($name)) {
				trigger_error($errmsg['class'].'"new \\'.$name.'()".',E_USER_NOTICE);
				return new $name($args);
			}
			# This will try and call a base framewofk class: $this->nFileHandler() references new \Nubersoft\nFileHandler()
			elseif(class_exists("\\Nubersoft\\{$name}")) {
				trigger_error($errmsg['class'].'"new '."\\Nubersoft\\{$name}".'()".',E_USER_NOTICE);
				$name	=	"\\Nubersoft\\{$name}";
				return new $name($args);
			}
			else {
				# Try and see if there is a data field with the called value $this->error_mode() same as $this->getDataNode('error_mode');
				if(!empty($this->getDataNode($name))) {
					trigger_error($errmsg['data'].'"$this->getDataNode(\''.$name.'\')".',E_USER_NOTICE);
					return $this->getDataNode($name);
				}
				elseif(function_exists($name)) {
					trigger_error($errmsg['func'],E_USER_WARNING);
					return (!empty($args))? $name(...$args) : $name();
				}
				# If none of this works, last resort it to throw an error
				else {
					trigger_error($errmsg['unknown'],E_USER_NOTICE);
					return false;	
				}
			}
		}
	}
	/**
	*	@description	Checks if a $_POST action equals input
	*/
	public	function postAction($action)
	{
		return $this->actionCompare($action);
	}
	/**
	*	@description	Checks if a $_GET action equals input
	*/
	public	function getAction($action)
	{
		return $this->actionCompare($action,"getGet");
	}
	/**
	*	@description	Checks if a $_REQUEST action equals input
	*/
	public	function requestAction($action)
	{
		return $this->actionCompare($action,"getRequest");
	}
	/**
	*	@description	Checks if a $_REQUEST action equals input
	*/
	public	function compareAction($action,$type="getPost")
	{
		$val	=	$this->{$type}('action');
		if(empty($val) || !is_string($val))
			return false;
		
		return ($val == $action);
	}
}