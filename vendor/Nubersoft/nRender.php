<?php
namespace Nubersoft;

class nRender extends \Nubersoft\nQuery
{
	use nUser\enMasse,
		Plugin\enMasse,
		Conversion\enMasse,
		Settings\enMasse,
		Settings\Page\enMasse;
	
	protected	$Html,
				$User,
				$sUser;
	
	public	function __construct()
	{
		$this->sUser	=	(!empty($this->getSession('user')))? $this->getSession('user') : [];
		$this->Html		=	$this->getHelper('Html');
		$this->User		=	$this->getHelper('nUser');
		return parent::__construct();
	}
	
	public	function userGet($key = false)
	{
		if(!empty($key))
			return (isset($this->sUser[$key]))? $this->sUser[$key] : false;
		
		return $this->sUser;
	}
	
	public	function getHeader()
	{
		$data	=	$this->getHelper('Settings\Controller')->getHeaderPrefs('html');
		if(!empty($data['toggle']) && $data['toggle'] == 'on') {
			return $this->getHelper('nMarkUp')->useMarkUp($this->dec($data['value'])).PHP_EOL;
		}
	}
	
	public	function getFooter()
	{
		$data	=	$this->getHelper('Settings\Controller')->getFooterPrefs('html');
		
		if(!empty($data)) {
			return $this->getHelper('nMarkUp')->useMarkUp($this->dec($data)).PHP_EOL;
		}
	}
	
	public	function render()
	{
		$page	=	$this->getPage();
		$code	=	(!empty($this->getDataNode('header')['header_response_code']))? $this->getDataNode('header')['header_response_code'] : 200;
		# Set the response code here
		http_response_code($code);
		# If not admin, set to frontend template
		if(empty($this->getDataNode('routing')) || ($page['page_live'] != 'on' && !$this->isAdmin())) {
			$code	=	404;
			# Show error page
			$temp	=	'errors';
		}
		else {
			# Check if this current page is an admin page
			$is_admin	=	(!empty($this->getDataNode('routing')['is_admin']) && $this->getDataNode('routing')['is_admin'] == 1);
			$temp		=	(!$is_admin)? 'frontend' : 'backend';
		}
		# Get the layout
		$layout		=	(!empty($this->getDataNode('templates')[$temp]))? $this->getDataNode('templates')[$temp] : 'false';
		# Redirect
		if(!empty($page['auto_fwd']) && !$this->isAdmin()) {
			$page['auto_fwd']	=	trim($page['auto_fwd']);
			$Router		=	$this->getHelper('nRouter');
			$external	=	preg_match('/^http/i', $page['auto_fwd']);
			$redirect	=	(!$external)? $this->localeUrl($page['auto_fwd']) : $page['auto_fwd'];
			
			if($page['auto_fwd_post'] == 'off') {
				$Router->redirect($redirect);
			}
			else {
				if($page['auto_fwd_post'] == 'on') {
					if($this->isLoggedIn()) {
						$Router->redirect($redirect);
					}
				}
			}	
		}
		# Check for page permissions
		if($page['session_status'] == 'on') {
			if(!$this->isLoggedIn()) {
				# Loop through templates and render login page
				foreach($this->getDataNode('templates')['paths'] as $path) {
					
					if(is_file($login = str_replace(DS.DS,DS,$path.DS.$temp.DS.'login.php'))) {
						# Render layout
						echo parent::render($login, $this);
						exit;
					}
				}
			}
		}
		# If the page requires admin access
		if($page['is_admin'] == 1 && !$this->isAdmin()) {
			# If not admin, redirect to home page
			$this->getHelper('nRouter')->redirect($this->localeUrl());
		}
		# Stop processing if ajax.
		if($this->isAjaxRequest()) {
			# If nothing has happened by now, it's not going to
			$this->ajaxResponse([
				"alert" => "No actions to take, you may have been logged out.",
				"html" => [
					"<script>window.location='/';</script>"
				],
				"sendto" => [
					"body"
				]
			]);
		}
		else {
			# Check if the page is being cached
			if($page['auto_cache'] == 'on' && !$this->isAdmin()) {
				# See if the user is logged in and set name
				$usergroup		=	(!empty($this->getSession('user')['usergroup']))? $this->getSession('user')['usergroup'] : 'loggedout';
				# See if locale is set
				$locale			=	(!empty($this->getSession('site')['locale']))? $this->getSession('site')['locale'] : 'USA';
				# Create the cache destination
				$destination	=	strtolower(NBR_CLIENT_CACHE.DS.'page'.DS.md5(json_encode($this->getGet())).DS.$locale.DS.$usergroup.DS.$page['ID'].".html");
				# Create the cache path
				$this->isDir(pathinfo($destination, PATHINFO_DIRNAME), true);
				$Cache	=	$this->getHelper('nCache');
				$Cache->start($destination);
				if(!$Cache->isCached()) {
					echo parent::render($layout, $this);
				}
				echo $Cache->render();
			}
			else
				# Render layout
				echo parent::render($layout, $this);
		}
	}
	
	public	function getContent()
	{
		$unique_id	=	(!empty($this->getDataNode('routing')['unique_id']))? $this->getDataNode('routing')['unique_id'] : false;
		
		if(empty($unique_id))
			return false;
		
		return $this->select()->from('components')->where([
			['c' => 'ref_page', 'v'=> $unique_id, 'co' => 'AND'],
			['c' => 'page_live', 'v' => 'on']
		])->fetch();
	}
	
	public	function getTitle($default = false, $tags = true)
	{
		$title	=	(!empty($this->getDataNode('routing')['menu_name']))? $this->getDataNode('routing')['menu_name'] : false;
		
		if(empty($title)) {
			$title	=	$default; 
		}
		
		$title	=	'<title>'.$title.'</title>'.PHP_EOL;
		
		return (!$tags)? trim(strip_tags($title)) : $title;
	}
	
	public	function getMeta($add = false)
	{
		$meta	=	(!empty($this->getDataNode('routing')['page_options']['meta']))? $this->getDataNode('routing')['page_options']['meta'] : $this->getSitePreferences('header_meta');
		
		if(empty($meta) && empty($add))
			return false;
		
		$Html		=	$this->getHelper('Html');
		$storage	=	[];	
		foreach($add as $name => $content) {
			$storage[]	=	$Html->createMeta($this->dec($name), $this->dec($content));
		}
		
		return $this->dec($meta).PHP_EOL.implode('', $storage);
	}
	
	protected	function allowedAsset($type, $func)
	{
		if(!is_callable($func)) {
			trigger_error(__FUNCTION__.'($type, $func) requires $func to be a callable function.', E_USER_NOTICE);
			return false;
		}
		
		$data	=	(!empty($this->getDataNode('templates')['config'][$type]['include']))? $this->getDataNode('templates')['config'][$type]['include'] : false;
		
		if(empty($data))
			return false;
		
		foreach($data as $include) {
			$allow			=	false;
			$attr 			=	(!empty($include['@attributes']))? $include['@attributes'] : false;
			$is_local		=	(!empty($attr['is_local']) && $attr['is_local'] == 'true');
			$is_admin		=	(!empty($attr['is_admin']) && $attr['is_admin'] == 'true');
			$is_frontend	=	(!empty($attr['is_frontend']) && $attr['is_frontend'] == 'true');
			$is_backend		=	(!empty($attr['is_backend']) && $attr['is_backend'] == 'true');
			$page_id		=	(!empty($attr['page_id']) && $attr['page_id'] == $this->getPage('ID'));
			$page_path		=	(!empty($attr['page_path']) && (strtolower($attr['page_path']) == strtolower($this->getPage('full_path'))));
			$is_loggedin	=	(!empty($attr['logged_in']) && $attr['logged_in'] == 'true');
			$path			=	str_replace(str_replace(DS,'/', NBR_ROOT_DIR),'', $include['path']);
			
			if(empty($attr))
				$allow	=	true;
			else {
				if($this->isFrontEnd() && $is_frontend)
					$allow	=	true;
				
				if($this->isBackEnd() && $is_backend)
					$allow	=	true;
				
				if(empty($is_frontend) && empty($is_backend)) {
					$allow	=	(!isset($attr['page_id']) && !isset($attr['page_path']) && !isset($attr['is_admin']));
				}
				
				if($page_id || $page_path)
					$allow	=	true;
				
				if($is_loggedin && !$this->isLoggedIn())
					$allow	=	false;
				
				if($is_admin){
					# ONLY MODE – No back end, no front end, only editor view
					$allow	=	((empty($is_frontend) && empty($is_backend)) && !$this->isAdmin())? false : true;
				}
			}
			if($allow)
				$storage[]		=	$func($this->Html, $path, $is_local);
		}
		
		return (!empty($storage))? implode('', $storage) : false;
	}
	
	public	function headerJavaScript()
	{
		$html	=	$this->dec($this->getSitePreferences('header_javascript'));
		return	(!empty($html))? '<script>'.PHP_EOL.$html.PHP_EOL.'</script>'.PHP_EOL : false;
	}
	
	public	function headerStyleSheets()
	{
		$html	=	$this->dec($this->getSitePreferences('header_styles'));
		return	(!empty($html))? '<style>'.PHP_EOL.$html.PHP_EOL.'</style>'.PHP_EOL : false;
	}
	
	public	function javaScript()
	{
		return $this->allowedAsset('javascript', function($Html, $path, $is_local){			
			return $Html->createScript($path, $is_local);
		});
	}
	
	public	function styleSheets()
	{
		return $this->allowedAsset('stylesheet', function($Html, $path, $is_local){			
			return $Html->createLinkRel($path, $is_local);
		});
	}
	
	public	function getMastHead()
	{
		$html	=	$this->dec($this->getSitePreferences('header_html'));
		if(empty($html))
			return false;
		
		return ($this->getSitePreferences('header_html_toggle') == 'on')? $html : '';
	}
	
	public	function isFrontEnd()
	{
		$route		=	$this->getDataNode('routing');
		if(!isset($route['is_admin']))
			return true;
		$page_type	=	(!empty($this->getDataNode('routing')['is_admin']))? $this->getDataNode('routing')['is_admin'] : false;
		
		return ($page_type !== 1);
	}
	
	public	function isBackEnd()
	{
		return (empty($this->isFrontEnd()));
	}
	
	public	function getPage($key = false)
	{
		$data	=	$this->getDataNode('routing');
		
		if(empty($data))
			return false;
		
		if($key)
			return (isset($data[$key]))? $data[$key] : null;
		
		return $data;
	}
	
	public	function getTemplateFile($file = 'index.php', $type = 'frontend', $path = false)
	{
		foreach($this->getDataNode('templates')['paths'] as $dir) {
			if(is_file($inc = $this->toSingleDs($dir.DS.$type.DS.$file)))
				return ($path)? $inc : parent::render($inc);
		}
		
		return false;
	}
	
	public	function getFrontEndFrom($file = 'index.php', $path)
	{
		return parent::render(NBR_CLIENT_TEMPLATES.DS.$path.DS.'frontend'.DS.$file);
	}
	
	public	function getBackEndFrom($file = 'index.php', $path)
	{
		return parent::render(NBR_CLIENT_TEMPLATES.DS.$path.DS.'backend'.DS.$file);
	}
	
	public	function getFrontEnd($file = 'index.php', $path = false)
	{
		return $this->getTemplateFile($file, 'frontend', $path);
	}
	
	public	function getBackEnd($file = 'index.php', $path = false)
	{
		return $this->getTemplateFile($file, 'backend', $path);
	}
	
	public	function getSitePreferences($key = false)
	{
		$prefs	=	$this->getHelper('Settings\Controller')->getSettingContent('system');
		
		if(!empty($key))
			return (!empty($prefs[$key]['option_attribute']))? $prefs[$key]['option_attribute'] : false;
		
		return $prefs;
	}
	
	public	function signUpAllowed()
	{
		return ($this->getSitePreferences('sign_up') == 'on');
	}
	
	public	function user($key = false)
	{
		$SESSION	=	$this->getDataNode('_SESSION');
		
		if(empty($SESSION))
			return false;
		
		$user	=	(!empty($SESSION['user']))? $SESSION['user'] : false;
		
		if(!empty($key))
			return (!empty($user[$key]))? $user[$key] : false;
		
		return $user;
	}
	
	public	function useAuth2()
	{
		$auth2		=	false;
		$authType	=	$this->getSystemOption('two_factor_auth');
		if($authType == 'off')
			return $auth2;
		$adminPg	=	($this->getPage('is_admin') == 1);

		if($authType == 'both')
			$auth2	=	true;
		elseif($authType == 'admin' && $adminPg)
			$auth2	=	true;
		elseif($authType == 'frontend' && !$adminPg)
			$auth2	=	true;
		
		return $auth2;
	}
}