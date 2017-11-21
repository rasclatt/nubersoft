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
*SNIPPETS:**
*	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
*	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
*	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;

class nRender extends \Nubersoft\nApp
{
	private	$page,
			$data,
			$nTemplate;

	private	static	$settings,
					$config,
					$page_settings;

	public	function __construct()
	{
		# Fetches the template obj
		$this->nTemplate	=	$this->getHelper('nTemplate');
		# Tries to create some header prefrences
		if(empty(self::$page_settings))
			$this->getHeadPrefs();
		# Returns original construct()
		return parent::__construct();
	}

	public	function thumbnail($path,$h = 30,$w = 30,$max = false,$gray=false)
	{
		$max	=	(!empty($max))? 'max-' : '';
		$gray	=	(!empty($gray))? ' -webkit-filter: grayscale(100%); filter: grayscale(100%);' : '';

		return $this->getHelper('nImage')->image($path,array('style'=>$max.'height: '.$h.'px; '.$max.'width: '.$w.'px;'.$gray,'class'=>'nbr_thumbnail'),false,false);
	}
	/**
	*	@description	Fetches a template method to create a path
	*	@param	$type	[sring]	FrontEnd or BackEnd
	*	@param	$content	[any]	Includes content for the method to use
	*/
	public	function getTemplateByType($type,$content = false)
	{
		return $this->nTemplate->{"get{$type}"}($content);
	}
	/**
	*	@description	Alias to getTemplateByType() using FrontEnd with root path added
	*	@param	$content	[any]	Includes content for the method to use
	*/
	public	function getFrontEnd($content=false)
	{
		return $this->toSingleDs(NBR_ROOT_DIR.DS.$this->getTemplateByType('FrontEnd',$content));
	}
	/**
	*	@description	Alias to getTemplateByType() using BackEnd with root path added
	*	@param	$content	[any]	Includes content for the method to use
	*/
	public	function getBackEnd($content=false)
	{
		return $this->toSingleDs(NBR_ROOT_DIR.DS.$this->getTemplateByType('BackEnd',$content));
	}

	public	function getCss()
	{
		return NBR_ROOT_DIR.DS.$this->nTemplate->getTemplateFrom('css');
	}

	public	function getJs()
	{
		return NBR_ROOT_DIR.DS.$this->nTemplate->getTemplateFrom('js');
	}
	/**
	*	@description	Returns back the template engine to get the current state of the nTemplate engine
	*/
	public	function getTemplateEngine()
	{
		return $this->nTemplate;
	}
	/**
	*	@description	Core tool to fetch a file path from the current template/{name}/plugins directory
	*	@args	[string | string]	main directory name | file within the plugin (default is index.php)
	*/
	public	function getTemplatePlugin()
	{
		$opts	=	func_get_args();
		$dir	=	(!empty($opts[0]))? $opts[0] : false;
		$append	=	(!empty($opts[1]))? $opts[1] : 'index.php';
		$path	=	$this->toSingleDs(NBR_ROOT_DIR.DS.$this->nTemplate->getTemplateFrom('plugins'.DS.$dir,$append));

		return $path;
	}
	/**
	*	@description	Checks to see if a plugin exists
	*/
	public	function templatePluginExists($dir,$append = 'index.php')
	{
		return is_file($this->toSingleDs($this->getTemplatePlugin($dir,$append)));
	}
	/**
	*	@description	Fetches the current plugin data extracted from the shortcode matching in the view
	*/
	public	function getPluginShortCode($key=false)
	{
		# Fetch the current plugin
		$pluginNode	=	$this->toArray($this->getDataNode('current_matched_plugin_content'));
		# Stop if empty
		if(empty($pluginNode))
			return false;
		# Filter
		$pregd	=	array_filter($pluginNode);
		# Send back if key requested
		if(!empty($key))
			return (isset($pregd[$key]))? $pregd[$key] : false;
		# Remove the plugin content
		$this->saveSetting('current_matched_plugin_content',false,true);
		# Return the fresh content
		return array_values($pregd);
	}
	/**
	*	@description	Fetches the plugin from the current template folder
	*					/client/template/{$current}/plugins/{$dir}/{$append}
	*/
	public	function useTemplatePlugin($dir,$append = 'index.php',$obj = false)
	{
		$layout	=	$this->useGlobalPlugin($dir,$append);

		if(!empty($layout))
			return $layout;

		$path	=	$this->toSingleDs($this->getTemplatePlugin($dir,$append));
		$render	=	parent::render($path,$obj);
		$this->saveSetting('current_matched_plugin_content',false,true);

		return $render;
	}
	/**
	*	@description	Same function as useTemplatePlugin() only the path is found in
	*					/client/template/plugins/{$dir}/{$append}
	*/
	public	function useGlobalPlugin($dir,$append = 'index.php')
	{
		$path	=	$this->toSingleDs(NBR_CLIENT_DIR.DS.'template'.DS.'plugins'.DS.$dir.DS.$append);
		if(is_file($path))
			return parent::render($path);

		return false;
	}

	public	function error404($error404,$message = false)
	{
		if(is_file($error404))
			return parent::render($error404,$message);
		elseif(is_string($error404))
			return $error404;
	}

	public	function siteOffline($options)
	{
		if(!is_file($options['filename']))
			throw new \Exception('File not found.');

		return $this->useData($options)->render($options['filename']);
	}

	public	function renderView()
	{
		unset(NubeData::$settings->configs);
		unset(NubeData::$settings->xml_add_list);
		echo $this->render($this->getTemplate());
	}

	public	function render()
	{
		$args		=	func_get_args();
		$include	=	(!empty($args[0]))? $args[0] : false;
		$useData	=	(!empty($args[1]))? $args[1] : false;
		$type		=	(!empty($args[2]))? $args[2] : 'include';
		
		if(!$include) {
			trigger_error('File is invalid',E_USER_NOTICE);
			if($this->isAdmin())
				echo printpre(__FUNCTION__);
			return false;
		}
		
		ob_start();
		switch($type) {
			case('include'):
				include($include);
				break;
			case('include_once'):
				include_once($include);
				break;
			case('require'):
				require($include);
				break;
			case('require_once'):
				require_once($include);
				break;
		}
		
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}
	
	public	function getDefault()
	{
		$template	=	DS.'core'.DS.'template'.DS.'default';
		$prefs		=	$this->toArray($this->getSitePrefs());

		if(is_array($prefs)) {
			$prefs	=	$this->getMatchedArray(
							array('content','template_folder'),
							'_',
							$prefs
						);
		}

		return (!empty($prefs['template_folder'][0]))? $prefs['template_folder'][0] : $template;
	}
	/**
	*	@description	Fetches and renders a template file within the current/global/default template folder
	*	@param	$template	[string]	Name of the file
	*	@param	$type	[string]	Used to determine if the template should grab from the front or admin dir
	*/
	public	function getTemplateDoc($template,$type = 'frontend')
	{
		$this->data	=	(!empty($this->data))? $this->data : false; 
		$inc		=	($type == 'frontend')? $this->getFrontEndPath($template) : $this->getBackEndPath($template);
		# If file is good, render
		if(is_file($inc))
			return parent::render($inc,$this->data);
		# Throw exception
		throw new \Exception('Template file not found: '.$this->Safe()->encode($inc));
	}
	/**
	*	@description	Fetches the config file
	*/
	protected	function getConfig()
	{
		# Returns if already applied
		if(!empty(self::$config))
			return self::$config;
		# Fetches and sets the config to memory
		$config			=	$this->toSingleDs($this->getTemplatePath(DS.'settings'.DS.'config.xml'));
		self::$config	=	$config;
		return	$config;
	}
	/**
	*	@description	Determines if the page is to be admin
	*/
	private	function determineAdminStatus($page)
	{
		if(empty($page)) {
			if(!$this->isAdmin())
				return false;
		}
		elseif(empty($page['is_admin'])) {
			if(!$this->isAdmin())
				return false;
		}
		elseif($page['is_admin'] == 2) {
			if(!$this->isAdmin())
				return false;
		}

		return true;
	}
	/**
	*	@descrtription	Fetches the current default template path
	*/
	public	function getTemplateBase()
	{
		if(!isset(self::$page_settings['current_base_template']))
			self::$page_settings['current_base_template']	=	pathinfo($this->getSite('template_current'),PATHINFO_DIRNAME);

		return	self::$page_settings['current_base_template'];
	}
	/**
	*	@description	Creates a standard path similar to the nApp version. This is more to store
	*					json/css/js preferences in cache
	*/
	protected	function getStandardMediaPath($content)
	{
		return $this->getDefaultIncludes('get_standard_media_path',$content);
	}
	/**
	*	@description	Fetches the config file and finds a media layout based on the template xml config
	*	@param	$type	[string]	This is the type that is to be returned in the template config
	*/
	public	function getMediaSrc($type = 'stylesheet',$frontend = false)
	{
		# create a cache file path
		$cached	=	$this->getStandardMediaPath(array('type'=>$type));
		$skip	=	false;
		if(is_file($cached) && !$skip)
			$contents	=	$this->getJson($cached);
		else {
			$config	=	$this->getConfig();
			$found	=	array();
			# See if there is a matching config file
			if(is_file($config))
				$parseConfig	=	$this->getHelper('nRegister')->parseXmlFile($config);
			# Get the automator to process the xml
			$nAutomator	=	$this->getHelper('nAutomator',$this);
			# Extract all arrays that have a matching key
			$this->flattenArrayByKey($parseConfig,$found,$type);
			# Fetch page data
			$page		=	$this->getPageURI();
			$is_front	=	(empty($this->getPageURI('is_admin')) || $this->getPageURI('is_admin') > 1);
			# If there are arrays to process
			if(!empty($found)) {
				# Get our html helper
				$nHtml	=	$this->getHelper('nHtml');
				# Count how many arrays were found
				$count	=	count($found);
				# Loop through those arrays and process results
				for($i=0; $i < $count; $i++) {
					if(!isset($found[$i]['include'][0]))
						$found[$i]['include']	=	array($found[$i]['include']);
					foreach($found[$i]['include'] as $key => $value) {
						$version	=	
						$local		=	true;
						if(isset($value['@attributes'])) {
							$attr	=	$value['@attributes'];
							if(isset($attr['is_admin'])) {
								if($attr['is_admin'] == 'true') {
									if(!$this->determineAdminStatus($page))
										continue;
								}
								else {
									if($this->determineAdminStatus($page))
										continue;
								}
							}

							if(isset($attr['logged_in']) && $attr['logged_in'] == 'true') {
								if(!$this->isLoggedIn())
									continue;
							}

							if(isset($attr['is_local']) && $attr['is_local'] == 'false')
								$local		=	false;

							if(isset($attr['version']) && $attr['version'] == 'false')
								$version	=	false;

							if(isset($attr['loadpage'])) {
								if($attr['loadpage'] != $this->getPageURI('full_path'))
									continue;
							}

							if(isset($attr['loadid'])) {
								if($attr['loadid'] != $this->getPageURI('ID'))
									continue;
							}

							if(isset($attr['frontend'])) {
								if($attr['frontend'] == 'true'){
									$display	=	($is_front);
								}
								else {
									$display	=	(!$is_front);
								}

								if(!$display)
									continue;
							}
							elseif(isset($attr['backend'])) {
								if($attr['backend'] == 'false'){
									$display	=	($is_front);
								}
								else {
									$display	=	(!$is_front);
								}

								if(!$display)
									continue;
							}

							if(isset($attr['state'])) {
								if($attr['state'] == 'offline') {
									if($this->siteLive())
										continue;
								}
							}

							if(isset($attr['action'])) {
								$requestMethod	=	'getPost';
								if(isset($attr['type'])) {
									switch($attr['type']) {
										case('request'):
											$requestMethod	=	'getRequest';
										case('get'):
											$requestMethod	=	'getGet';
									}
								}

								if($this->{$requestMethod}('action') != $attr['action']) {
									continue;
								}
							}
						}

						$path			=	$nAutomator->matchFunction($value['path']);

						$new[$type][]	=	($type == 'javascript')? $nHtml->javaScript($path,$local,$version) : $nHtml->styleSheet($path,$local,$version);
					}
				}
			}

			$contents	=	(!empty($new[$type]))? implode('',$new[$type]) : false;

			if(!empty($contents)) {
				$this->saveFile(json_encode($contents),$cached);
			}
		}

		return $contents;
	}

	public	function returnTemplatePath($name,$file = false,$frontend = false)
	{
		$file	=	(!empty($file))? $file : $name.'.php';
		$stored	=	$this->getStoredTemplatePath($name);
		if(!empty($stored))
			return $stored;

		$stored = $this->getTemplateFile($file,$frontend);
		$this->setStoredTemplatePath($name,$stored);
		return $stored;
	}

	public	function getStoredTemplatePath($name)
	{
		if(isset(self::$settings[$name]))
			return	self::$settings[$name];
	}

	public	function setStoredTemplatePath($name,$value)
	{
		if(!isset(self::$settings[$name]))
			self::$settings[$name]	=	$this->toSingleDs($value);
	}

	public	function getTemplateFile($filepath=false,$frontend='frontend')
	{
		$site		=	$this->getData()->getSite();

		if(!isset($site->default_template_dir)) {
			self::call('GetSitePrefs')->setPageRequestSettings();
			$site	=	$this->getData()->getSite();
		}

		$default	=	$site->default_template_dir.DS;
		$current	=	(isset($site->template))? pathinfo($site->template,PATHINFO_DIRNAME) : $default;
		$root		=	NBR_ROOT_DIR.DS;

		if(empty($filepath))
			return $current;

		$backend	=	DS.$frontend.DS.$filepath;

		if(is_file($file = $this->toSingleDs($root.$current.$backend)))
			return $file;
		else {
			$file = $this->toSingleDs($root.$default.$backend);
			return $file;
		}
	}
	/**
	*	@description	Renders the header
	*	@param	$dir [string{frontend | admintools}] The template folder to find the document in
	*	@param	$serttings	[array|bool{false}]	Settings/data to pass to the templater
	*/
	public	function getHeader($dir='frontend',$settings = false)
	{
		return $this->setDefaultRenderTemplate($settings,'head',$dir);
	}
	/**
	*	@description	Renders the footer
	*	@param	$dir [string{frontend | admintools}] The template folder to find the document in
	*	@param	$serttings	[array|bool{false}]	Settings/data to pass to the templater
	*/
	public	function getFooter($dir='frontend',$settings = false)
	{
		return $this->setDefaultRenderTemplate($settings,'foot',$dir);
	}
	/**
	*	@description	Renders content from a template folder
	*	@param	$serttings
	*	@param	$kind
	*	@param	$dir
	*	@param	$force
	*/
	public	function setDefaultRenderTemplate($settings,$kind,$dir = 'frontend',$force = false)
	{
		$html	=	(!empty($settings['html']))? $settings['html'] : false;

		if(!$html) {
			if(!empty($settings['link'])) {
				if(is_file($settings['link']))
					$link = $settings['link'];
			}

			if(empty($link)) {
				# Fetches the template front or backend
				$usePage		=	(!empty($this->getHtml('template')['is_admin']))? $this->getBackEnd($kind.'.php') : $this->getFrontEnd($kind.'.php');
				$root			=	NBR_ROOT_DIR.DS;
				$frontend		=	$dir.DS.$kind.'.php';
				$templatePath	=	$usePage;	
				$default		=	$this->toSingleDs($root.$this->getHtml('template')['default_template_dir'].DS.$frontend);
				$link			=	(is_file($templatePath) && !$force)? $templatePath : $default;
			}

			ob_start();
			include_once($link);
			$html	=	ob_get_contents();
			ob_end_clean();	
		}

		if($html)
			return $this->getFunction('use_markup',$html);
	}

	private	function processMeta($array)
	{
		$nHtml	=	$this->getHelper('nHtml');
		$Safe	=	self::call('Safe');
		foreach($array as $name => $content) {
			if(empty($content))
				continue;

			$new[]	=	$nHtml->getHtml('meta',array('name'=>$Safe->decode($name),'content'=>$Safe->decode($content)));
		}

		return (!empty($new))? $new : array();
	}

	public	function addHeadPrefAttr($key,$value)
	{
		self::$page_settings[$key]	=	$value;
	}

	public	function getHeadPrefs()
	{
		$nSafe		=	$this->getHelper('Safe');
		$template	=	$this->getSite();
		$getPrefs	=	$this->getData()->getPreferences();
		$page		=	$this->getDataNode('pageURI');

		if(empty($getPrefs) && empty(constant('NBR_PLATFORM')))
			$this->getHelper('CoreMySQL')->installRows('system_settings');

		# Incase there are no settings
		if(empty($getPrefs) && !empty(constant('NBR_PLATFORM'))) {
			self::$page_settings	=	array(
				'front'=>'',
				'back'=>'',
				'prefs'=>'',
				'template'=>'',
				'page'=>'',
				'meta'=>'',
				'title'=>'',
				'header'=>'',
				'inline_css'=>'',
				'favicons'=>'',
				'javascript'=>'',
				'js_lib'=>''
			);

			return $this;
		}

		foreach($getPrefs as $title => $obj) {
			${str_replace('settings_','',$title)}	=	$obj->content;
		}

		$prefs['page']		=	$page;
		$prefs['site']		=	$site;
		$prefs['header']	=	$head;
		$prefs['template']	=	(!empty($template) && !empty($template->template))? pathinfo($template->template,PATHINFO_DIRNAME) : $this->getTemplateFile();

		$prefs		=	$this->toArray($prefs);
		$template	=	$this->toArray($template);
		$page		=	$this->toArray($page); 
		$meta		=	false;
		if(!empty($page['page_options']['meta'])) {
			$meta	=	$this->processMeta($page['page_options']['meta']);
			$meta	=	(!empty($meta))? implode(PHP_EOL,$meta).PHP_EOL : false;
		}
		# This pulls from the default settings in site prefs
		if(empty($meta) && !empty($head->meta))
			$meta	=	$this->safe()->decode($head->meta);

		$page_menu	=	(isset($page['menu_name']))? $page['menu_name'] : $this->siteUrl();
		$title		=	(!empty($meta['title']))? $nSafe->decode($meta['title']) : $page_menu;
		$header		=	(!empty($prefs['header']))? $nSafe->decode($prefs['header']) : false;
		$inlineCss	=	(!empty($header['style']))? $nSafe->decode($header['style']).PHP_EOL : false;
		$favicons	=	(!empty($header['favicons']))? $nSafe->decode($header['favicons']) : false;
		$javascript	=	(!empty($header['javascript']))? $nSafe->decode($header['javascript']) : false;
		$js_lib		=	(!empty($header['javascript_lib']))? $nSafe->decode($header['javascript_lib']) : false;

		self::$page_settings	=	array(
			'front'=>$this->getFrontEnd(),
			'back'=>$this->getBackEnd(),
			'prefs'=>$prefs,
			'template'=>$template,
			'page'=>$page,
			'meta'=>$meta,
			'title'=>$title,
			'header'=>$header,
			'inline_css'=>$inlineCss,
			'favicons'=>$favicons,
			'javascript'=>$javascript,
			'js_lib'=>$js_lib
		);

		return $this;
	}

	public	function getHtml($key=false)
	{
		if(!empty($key))
			return (isset(self::$page_settings[$key]))? self::$page_settings[$key] : false;

		return self::$page_settings;
	}

	public	function error404Page()
	{
		return $this->useTemplatePlugin('error_404_page');
	}

	public	function maintenancePage()
	{
		$display	=	$this->getFrontEndPath('maintenance.php');

		if(!is_file($display))
			throw new \Nubersoft\nException('Error template path is invalid: '.$display);

		return parent::render($display);
	}
	/**
	*	@description	Checks if the current page requires user to be logged in, then checks
	*					if the usergroup of the current user. If usergroup is more (higher is less permission),
	*					then forbidden
	*/
	public	function isForbidden()
	{
		# Get the user's usergroup
		$usergroup	=	$this->getUsergroup($this->getSession('usergroup'));
		# Get the page's usergroup
		$page		=	$this->getUsergroup($this->getPageURI('usergroup'));
		# Get the login requirement of the page
		$login		=	$this->getPageURI('session_status');
		# If the content requires login
		if($login == 'on')
			# If usergroup is greater than the page usergroup
			return ($usergroup > $page);
		# Return no problem
		return false;
	}
	/**
	*	@description	Checks if the current page is valid
	*/
	public	function isPageNotFound()
	{
		if(empty($this->getDataNode('pageURI')))
			return	true;

		$page_live	=	$this->getDataNode('pageURI')->page_live;
		if($page_live != 'on')
			return true;

		return false;
	}

	public	function getViewPort($content = 'width=device-width')
	{
		$meta	=	array(
			'name'=>'viewport',
			'content'=>$content
		);
		return $this->getHelper('nHtml')->getHtml('meta',$meta).PHP_EOL;
	}
	/**
	*	@description	Creates a cache block
	*	@param $content [string]	Content to cache
	*	@param $path [string]	Path to save the content
	*	@param $use_raw_path [string]	Use the full path injected otherwise use the standard cache path
	*/
	public	function cacheBlock($content,$path,$use_raw_path = false)
	{
		$gPath	=	($use_raw_path)? $path : $this->getStandardPath(DS.$path);
		# Create the cache file path
		$path	=	$this->toSingleDs($gPath);
		# Get cache engine
		$cache	=	$this->getHelper('nCache');
		# If there is a header file, just get it. If not, make it
		$cache->cacheBegin($path);
		# This is what will be cached
		if(!$cache->isCached())
			echo $content;
		# Return the output
		return $cache->cacheRender();
	}
	/**
	*	@description	Create an anonymous function cacher
	*	@param	$path [string]	Path to save the cached content
	*	@param	$func [function]	Anonymous function to create content
	*	@param	$use_raw_path [bool]	Tells the method to use the raw path
	*/
	public	function cacheLayout($path,$func,$use_raw_path = true)
	{
		if(!is_callable($func)) {
			trigger_error('Second parameter of '.__CLASS__.__FUNCTION__.' must be a function.',E_USER_NOTICE);
			return false;
		}
		
		return $this->cacheBlock($func($this),$path,$use_raw_path);
	}
	/**
	*	@description	Creates the company logo from the template folder
	*	@param	$settings [array]	Image attributes
	*/
	public	function renderSiteLogo($settings=false)
	{
		$path	=	$this->getFrontEnd('images'.DS.'default.png');
		if(!is_file($path)) {
			trigger_error('Default logo not found: '.$path, E_USER_NOTICE);
			return false;
		}
		
		$nImage	=	$this->getHelper('nImage');
		$imgsrc	=	$nImage->toBase64($path);
		if(!empty($imgsrc)) {
			$file	=	$nImage->image($imgsrc,$settings,false,false);
			return $file;
		}
	}

	public	function renderAdminLogo($settings=false,$version = 'nubersoft.png')
	{
		$nImage	=	$this->getHelper('nImage');
		$imgsrc	=	$nImage->toBase64(NBR_MEDIA_IMAGES.DS.'logo'.DS.$version);
		if(!empty($imgsrc)) {
			$file	=	$nImage->image($imgsrc,$settings,false,false);
			return $file;
		}
	}

	public	function getSiteOfflineMsg($message = false)
	{
		$message	=	(!empty($message))? $message : 'Site is under maintenance';
		return (isset($this->getPrefsContent('site')->site_live->value))? $this->getPrefsContent('site')->site_live->value : $message;
	}

	public	function setCachePath($name,$array = false)
	{
		if(is_array($array)) {
			return implode(DS,$array).DS.$name;
		}

		return $this->getStandardPath(DS.$name);
	}

	public	function getMedia($type,$path,$options=false,$corepath=false)
	{
		$corepath	=	(!empty($corepath))? $corepath : NBR_MEDIA;
		switch($type) {
			case('image'):
				$use	=	'images';
				break;
			default:
				$use	=	$type;
		}

		$path	=	$this->toSingleDs($corepath.DS.$use.DS.$path);

		switch($type) {
			case('image'):
				$version	=	(isset($options['version']))? $options['version'] : false;
				$local		=	(isset($options['local']))? $options['local'] : false;

				if(isset($options['version']))
					unset($options['version']);
				if(isset($options['local']))
					unset($options['local']);

				$layout	=	$this->getHelper('nImage')->image($path,$options,$version,$local);
				break;
		}

		return trim($layout);
	}

	public	function Accessibility($type = 'View')
	{
		return $this->getHelper('Accessibility\\'.$type);
	}
	/**
	*	@description	Create a tokenable document
	*/
	public	function dynamicMediaSrc($path,$key,$type='css')
	{
		$nOnce		=	$this->getHelper('nToken')->nOnce($key,$path)->getToken();
		$encPath	=	$this->localeURL('/index.php?action=nbr_media_file&controller='.$this->safe()->encOpenSSL(json_encode(array('token'=>$nOnce,'type'=>'css'))));
		switch($type) {
			case('css'):
				return $this->getHelper('nHtml')->styleSheet($encPath,false,false);
			case('js'):
				return $this->getHelper('nHtml')->javaScript($encPath,false,false);
		}
	}
	/**
	*	@description	Returns the site preferences from the data node and wraps it in the methodize class
	*/
	public	function getSitePreferences()
	{
		return $this->getMethodizeObj('preferences')->getSettingsSite();
	}
	/**
	*	@description	Returns the site preferences custom attributes (if available)
	*/
	public	function getCustomAttr()
	{
		return $this->getSitePreferences()->getContent()->getCustom();
	}

	public	function getMenus()
	{
		return $this->getMethodizeObj('menu_data');
	}

	public	function getMethodizeObj($lookup,$name = false)
	{
		if(empty($name))
			$name	=	$lookup;

		$prefs		=	$this->toArray($this->getDataNode($lookup));
		$Methodize	=	(new \Nubersoft\Methodize())->saveAttr($name,$prefs);
		return $Methodize->{$name}();
	}
	/**
	*	@description	Fetches a component from database based on column
	*/
	public	function getComponent()
	{
		$args	=	func_get_args();
		$type	=	(!empty($args[1]) && !is_bool($args[1]))? $args[1] : 'ref_spot';
		$value	=	(!empty($args[0]) && is_array($args[0]))? $args[0] : [$type=>$args[0]];
		foreach($args as $arg) {
			if(isset($limit))
				continue;
			
			if(is_bool($arg))
				$limit	=	$arg;
		}
		
		if(!isset($limit))
			$limit	=	false;
		
		$Component	=	$this->getPlugin('\nPlugins\Nubersoft\Component\Model');
		return $Component->getComponent($value,$limit);
	}
	/**
	*	@description	Fetches the social media links
	*/
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
	/**
	*	@description	Alias of the settings controller same-named method
	*/
	public	function isDevMode()
	{
		$class	=	'\nPlugins\Nubersoft\Settings\Controller';
		return $this->getPlugin($class)->{__FUNCTION__}();
	}
	/**
	*	@description	Used in the site prefs page
	*/
	public	function getSiteContent()
	{
		$prefs	=	$this->getSitePrefs();

		return (isset($prefs->content))? $prefs->content : false;
	}
	/**
	*	@description	Render a block origin
	*/
	public	function showBlockOrigin($path)
	{
		return ($this->editorModeActive())? '<div class="nbr_origin_item">'.$this->stripRoot($path).'</div>' : '';
	}
	/**
	*	@description	Checks if editor is active
	*/
	public	function editorModeActive()
	{
		if(!empty($this->getSession('admintools')->editor))
			return ($this->getSession('admintools')->editor == 'on');

		return false;
	}
	/**
	*	@desctription	Fetches the footer prefs from the system_prefs table
	*/
	protected	function getFootPrefs()
	{
		$foot	=	[];
		if(!empty($this->getDataNode('preferences')->settings_foot->content))
			$foot	=	$this->toArray($this->getDataNode('preferences')->settings_foot->content);
		# Return prefs
		return	$foot;
	}
	/**
	*	@description	Renders the html content pulled from the head prefs from system_prefs
	*/
	protected	function renderFoot()
	{
		$foot	=	$this->getFootPrefs();
		if(empty($foot['html']['value']))
			return false;
		
		return ($foot['html']['toggle'] == 'on')? $this->safe()->decode($foot['html']['value']) : false;
			
	}
}