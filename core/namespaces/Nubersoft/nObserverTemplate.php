<?php
namespace Nubersoft;

class nObserverTemplate extends \Nubersoft\nRender implements nObserver
	{
		private	$page,
				$site;
		
		public	function listen()
			{
				$this->page	=	$this->getPage();
				$this->site	=	$this->getSite();
				
				if(empty($page->unique_id)) {
					$admin		=	false;
					$template	=	false;
				}
				# Set admintools template
				elseif($this->isAdmin() && !$this->siteValid()) {
					$template	=	false;
					$admin		=	true;
				}
				elseif(!isset($this->page->template)) {
					$template	=	false;
					$admin		=	$this->siteValid();
				}
				elseif(isset($this->page->is_admin) && $this->page->is_admin == 1) {
					$admin		=	true;
					$template	=	(!empty($this->page->template))? NBR_ROOT_DIR.DS.$this->page->template : false;
				}
				else {
					$admin		=	false;
					$template	=	(isset($this->page->template))? NBR_ROOT_DIR.DS.$this->page->template : false;
				}
				# If the page is admin, check if there is an associated whitelist
				if($admin){
					if(!$this->onWhiteList($_SERVER['REMOTE_ADDR'])) {
						$this->autoload('nbr_fetch_error',NBR_FUNCTIONS);
						# create a log file
						self::call('nLogger')->ErrorLogs_WhiteList(nbr_fetch_error('whitelist',__FILE__,__LINE__));
					}
				}
				# Send 404 error
				if(empty($this->page->unique_id) && !$this->isAjaxRequest())
					$this->saveSetting('error404',array('title'=>'Error 404','body'=>'Page not found'),true);
				# Return a template directory
				if(!empty($site->template))
					$this->site->template	=	$this->getTemplate($template,$admin);
				else
					$this->saveSetting('site',array('template'=>$this->getObserverTemplate($template,$admin)));
			}
		/*
		**	@description	Create default header for file not found
		*/
		public	function setRoutingHeaders()
			{
				if(empty($this->getPageURI('ID'))) {
					if(!$this->isAjaxRequest()) {
						if(empty($this->getPageURI('full_path'))) {
							$this->getHelper('nRouter')
								->addHeaderCode(404)
								->addHeader('http/1.1 404 not found');
						}
					}
				}
			}
		/*
		**	@description	This method is used to output the final template to the browser
		**
		*/
		public	function toBrowser()
			{
				$SESSION	=	$this->getHelper('nSessioner');
				if(!$this->isAjaxRequest()) {
					echo $this->renderView();
					$SESSION->clearAlerts();
				}
				else {
					$SESSION->clearAlerts();
					$this->ajaxResponse(array('msg'=>'No event','data'=>$this->getRequest()));
				}
			}
		
		public	function getObserverTemplate($template = false,$admin = false,$check = false)
			{
				if(!empty($this->page))
					$payload	=	$this->page;
				else
					$payload	=	(!empty($this->getPage()))? $this->getPage() : $this->toObject(array());
				
				$site_tFolder		=	$this->getDefaultTemplate();
				$default_tFolder	=	$this->getDefaultTemplate();
				# If template not empty, use that or else use default dir
				$template_dir		=	(empty($default_tFolder))? $template : $default_tFolder;
				# Assign page names
				# Default load page
				$page['temp']		=	DS.'include.php';
				# Admintools page
				$page['admin']		=	DS.'admintools'.DS.'index.php';
				# Offline page
				$page['offline']	=	DS.'frontend'.DS.'site.live.php';
				# Login page
				$page['login']		=	DS.'frontend'.DS.'site.login.php';
				# If sub template set, assign
				$page['use']		=	(!empty($payload->use_page))? $payload->use_page : false;
				# Return if subpage set and is in place
				if(!empty($page['use']) && is_file($gopage = str_replace(DS.DS,DS,$template_dir.DS.$page['use']))) {
					$this->saveSetting('site',array('use_page'=>$gopage));
					return $gopage;
				}
				# See if page live
				$pLive	=	($this->getPage('page_live') == 'on');
				# Check if the template is valid or is not live and user is admin
				if(empty($payload->unique_id) || (!$pLive && !$this->isAdmin())) {
					# Check if there is a custom error page
					if(is_file($useErr = $default_tFolder.DS.'frontend'.DS."error404.php")) {
						$this->saveSetting('site',array('error_404'=>$useErr));
						return $useErr;
					}
					else
						return $site_tFolder.DS.'error404.php';
				}
				
				if(!empty($payload->session_status)) {
					if($payload->session_status == 'on') {
						$usegroup	=	(!empty($payload->usergroup) && is_numeric($payload->usergroup))? $payload->usergroup : NBR_WEB;
						if(empty($this->getFunction('is_loggedin'))) {
							$loginpg	=	$this->toSingleDs($template_dir.DS.$page['login']);
							$gopage		=	(is_file($loginpg))? $loginpg : $site_tFolder.$page['temp'];
							return $gopage;
						}
					}
				}
				# Determine which type of page to return
				if($admin)
					$gopage = (is_file($usefile = $template_dir.DS.$page['admin']))? $usefile : $site_tFolder.DS.$page['admin'];
				else
					$gopage = (is_file($usefile = $template_dir.DS.$page['temp']))? $usefile : $site_tFolder.DS.$page['temp'];
				# Return
				return str_replace(DS.DS,DS,$gopage);
			}
		/*
		**	@description	Runs the offline page and the 404 page
		*/
		public	function offline()
			{
				# Check if the site is offline (in maintenance)
				$live	=	$this->siteLive();
				$getPId	=	(!empty($this->getPageURI('ID')))? $this->getPageURI('ID') : false;
				# If the page is an admin page
				if($this->isAdminPage())
					$type	=	'admin';
				elseif(!empty($this->getDataNode('error404')))
					# Determines which function to call to render a page
					$type	=	'error';
				else
					$type	=	'maintenance';
				# Sets the path to the cache file
				$cached	=	$this->getCachedHtml($type.$getPId);
				
				if($this->isAjaxRequest()) {
					echo json_encode(array(
						'success'=>false,
						'errorcode'=>404,
						'html'=>array(
							'<h1>404 Not found</h1>'
						),
						'sendto'=>array(
							'body'
						)
					));
					
					exit;
				}
				else {	
					$order	=	array();
					# Get the automator
					$this->getHelper('nAutomator',$this)
						->setListenerName('action')
						->getInstructions('blockflow/timezone');
					
					switch($type) {
						case('admin'):
							echo $this->render($this->getBackEnd('index.php'));
							break;
						case('error'):
				
							//die(printpre($this->getDataNode()));
				
							if(!empty($cached))
								echo $cached;
							else {
								$html	=	$this->error404Page($cached);
								$this->cacheHtml($type.$getPId,$html);
								echo $html;
							}
							break;
						default:
							if(!empty($cached))
								echo $cached;
							else {
								$html	=	$this->maintenancePage();
								$this->cacheHtml($type.$getPId,$html);
								echo $html;
							}
					}
				}
			}
		/*
		**	@description	Fetch and processes site plugins
		*/
		public function getPagePlugins()
			{
				# Get pref file if exists
				$cache	=	$this->getPrefFile('plugins');
				# Check if there is plugins folder in the first place
				$dir	=	NBR_CLIENT_DIR.DS.'plugins';
				# If there is no plugin, the just skip
				if(!is_dir($dir))
					return false;
				# If there is no cache pref saved, fetch one
				if(empty($cache)) {
					# Get all the config files in the plugins folder
					$files	=	$this->getDirList(array(
						'dir'=>$dir,
						'type'=>array(
							'xml'
						)
					));
					# Isolate
					$xml	=	 (!empty($files['host']))? $files['host'] : array();
					# If there are none, stop
					if(empty($xml))
						return false;
					# Loop and parse config files
					foreach($xml as $path) {
						$configs[]	=	$this->getHelper('nRegister')->parseXmlFile($path);
					}
					# Set storage
					$new	=	array();
					# Extract all the modules
					$this->flattenArrayByKey($configs,$new,'module');
					# Save the array to pref file
					$this->savePrefFile('plugins',$new);
				}
				else
					# Just assign stored array
					$new	=	$cache;
				# Stop if not an array
				if(!is_array($new))
					return;
				# Loop and check for routing
				foreach($new as $object) {
					if(empty($object['@attributes']['type']))
						continue;
					elseif(!empty($object['@attributes']['page_live'])) {
						if(strtolower($object['@attributes']['page_live']) != 'on')
							continue;
					}
					# Simplify
					$type	=	strtolower($object['@attributes']['type']);
					# Assign current redirect path
					if(!isset($thisPage)) {
						$SERVER		=	$this->getDataNode('_SERVER');
						$REDIR		=	(isset($SERVER->REDIRECT_URL))? $SERVER->REDIRECT_URL : $SERVER->REQUEST_URI;
						$thisPage	=	$REDIR;
					}
					# Check that ther is a path set up
					$rPath	=	(!empty($object['object']['routing']['full_path']))? trim($object['object']['routing']['full_path']) : false;
					# If there is a path that matches the route
					if($thisPage == $rPath) {
						# Simplify
						$page	=	$object['object']['routing'];
						# Make sure there is an include to publish the page
						if(isset($page['include'])) {
							# Parse the link to make sure it's valid
							$page['include']	=	$this->getHelper('nAutomator',$this)->matchFunction($page['include']);
							if(is_file($page['include'])) {
								$this->getHelper('nRouter')->resetPageRouting($page);
							}
						}
						# Stop processing
						return;
					}
				}
			}
		/*
		**	@description	Checks for the site to be in different locales
		*/
		public	function getMultiSite()
			{
				# Forces to country signed up in
				if($this->isLoggedIn()) {
					
					if($this->getRequest('action') == 'logout') {
						$this->getHelper('nSessioner')->destroy();
						$this->getHelper('nRouter')->addRedirect($this->siteUrl($this->getPageURI('full_path')));
					}
					
					if(!empty($this->getSession('country'))) { //&& ($this->getSession('country') != 'USA')
						if($this->getLocale() != $this->getSession('country')) {
							$this->setSession('LOCALE',$this->getSession('country'),true);
							if(!$this->isAjaxRequest())
								$this->getHelper('nRouter')->addRedirect($this->localeUrl($this->getPageURI('full_path')));
						}
					}
				}
				
				$this->getHelper('nRouter')->appendAndRedirect();
			}
	}