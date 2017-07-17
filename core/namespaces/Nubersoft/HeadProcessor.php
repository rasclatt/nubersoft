<?php
/*Title: HeadProcessor*/
/*Description: This is the main processor*/
namespace Nubersoft;

class HeadProcessor extends \Nubersoft\nApp
	{
		public		$reset;
		
		protected	$payload,
					$nRouter,
					$nSessioner;
		
		private		$registry;
		
		public	function __construct()
			{
				$this->registry		=	$this->getRegistry();
				# Get the default router
				$this->nRouter		=	$this->getHelper('nRouter');
				$this->nSessioner	=	$this->getHelper('nSessioner');
				
				return parent::__construct();
			}
		
		protected	function processPrefs()
			{
				# Load the site variables processor
				$this->autoload('process_site_prefs');
				# Assign variables
				$this->payload	=	process_site_prefs();
				# Write to database
				$MySQLEngine	=	new DBWriter();
				$MySQLEngine->execute($this->payload);
			}
		
		protected	function createHtaccess()
			{
				$nReWriter	=	$this->getHelper('nReWriter');
				$script		=	$nReWriter->fetchHtaccess();
				if(empty($script)) {
					$nReWriter->getDefault();
					return;
				}
				
				$nReWriter->createHtaccess(array('content'=>$script,'save_to'=>NBR_ROOT_DIR));
			}
		/*
		**	@description	This method loops through the config file (registry.xml) and
		**					sets permissions for certain pages based on the settings
		**	@trigger		This is a listener for Admin Login ($this->isAdmin())
		*/
		public	function corePermissions()
			{
				# Stop if not admin, don't want to run it every time someone goes to the page
				if(!$this->isAdmin())
					return;
				# Fetch writer
				$Register	=	$this->getHelper('nReWriter');
				# Block public access
				$deny		=	$Register->getScript('serverReadWrite');
				# Allow public access overwrite
				$allow		=	$Register->getScript('browserRead');
				# If one of these are empty, stop
				if(empty($deny) || empty($allow))
					return;
				# Fetch the list of protected and unprotected folders
				$config	=	$this->getPrefFile('core_htaccess_prefs',array('save'=>true),false,function($path,$nApp) {
					$config				=	$nApp->getRegistry();
					$pref['protect']	=	(isset($config['protectdirectory']))? $nApp->getRecursiveValues($config['protectdirectory']) : array();
					$pref['allow']		=	(isset($config['unprotectset']))? $nApp->getRecursiveValues($config['unprotectset']) : array();
					
					return $pref;
				});
				# Loop through protected directories and save permissions
				foreach($config['protect'] as $dir) {
					$dir	=	DS.trim($dir,'/').DS.'.htaccess';
					if(is_file($dir))
						continue;
						
					$this->isDir(pathinfo($dir,PATHINFO_DIRNAME));
					
					if(file_put_contents($dir,$deny))
						$this->toAlert('Protection of directory set: '.$this->stripRoot($dir));
				}
				# Loop through the unprotected and add the allow script
				foreach($config['allow'] as $dir) {
					$dir	=	DS.trim($dir,'/').DS.'.htaccess';
					if(is_file($dir))
						continue;
					
					if(file_put_contents($dir,$allow))
						$this->toAlert('Protection removal of directory: '.$this->stripRoot($dir));
				}
			}
		
		private	function matchTempCreds($PasswordEngine)
			{
			}
		
		public	function login($skipRedirect = false,$ajaxRespond = false)
			{
				$logFilePath	=	array('client','settings','reporting','errorlogs','login.txt');
				$action			=	$this->getRequest('action');
				$allow			=	false;
				switch($action) {
					case('logout'):
						$allow				=	true;
						$_setting['logout']	=	1;
						break;
					case('loggin_remote'):
						$allow				=	($this->getPost('action') == 'loggin_remote');
						$_setting['remote']	=	1;
						break;
					default:
						$allow	=	($this->getPost('action') == 'login');
						if($allow)
							$_setting['login']	=	1;
						else
							$_setting	=	array();
				}
				$redPath		=	(!empty($this->getDataNode('_SERVER')->PHP_SELF))? $this->getDataNode('_SERVER')->PHP_SELF : $this->localeUrl();
				$reload_page	=	false;
				$redirect		=	$this->nRouter->stripIndex($redPath);
				
				# Destroy session and redirect
				if(isset($_setting['logout']) && $allow) {
					# Destroy the session
					$this->nSessioner->destroy();
					# See if there is a jumppage
					if($this->getPost('jumppage'))
						$this->processJumpPage();
					# See if there is a redirect set for this page
					$this->redirectOnForward(true);
					# If not, just redirect back to self
					$this->nRouter->addRedirect($redirect);
				}
				# See if there is a remote login attempt (try if install is set)
				elseif(isset($_setting['remote']) && $this->tokenMatch('login_remote')) {
					$reload_page	=	$this->loginRemotely();
				}
				# Check token
				elseif(isset($_setting['login']) && $allow) {
					$errMsg['missing']	=	'Login token missing. Reload the page to try again.';
					$errMsg['required']	=	'Token required to login.';
					# If token exists for login
					$tokenExists	=	$this->getHelper('nToken')->tokenExists('login');
					# If the token exists, valiate it
					if($tokenExists) {
						# If the token is empty, record and return							
						if(empty($this->getPost('token')->login)) {
							if($this->isAjaxRequest())
								$this->ajaxResponse(array('alert'=>$errMsg['missing']));
							
							$this->saveIncidental('login',array('bad_request'=>$errMsg['missing']),true);
							return;
						}
						# Create the token engine
						$Token		=	$this->getHelper('nToken');
						# Fetch the page token (just incase that token is sent)
						$nProcToken	=	$Token->getTokenFromPool('page','nProcessor');
						# Assign current token
						$loginToken	=	$this->getPost('token')->login;
						# Check if token is set and matches, then clear it
						$token		=	$Token->tokenMatch('login');
						# Check if the page token is a match
						$pageToken	=	(!empty($nProcToken) && ($nProcToken == $loginToken));
						
						# If the token is set, but does not match, just return
						if(!$token && !$pageToken) {
							if($this->isAjaxRequest())
								$this->ajaxResponse(array('alert'=>$errMsg['missing'].print_r($this->getSession('token'),1).print_r($this->getPost(),1).print_r($pageToken.'=>'.$loginToken,1)));
							
							$this->saveIncidental('login',array('bad_request'=>$errMsg['missing']),true);
							return;
						}
					}
					else {
						if($this->isAjaxRequest())
							$this->ajaxResponse(array('alert'=>$errMsg['required']));
								
						$this->saveIncidental('login',array('bad_request'=>$errMsg['required']),true);
						return false;
					}
				}	
				
				# Self-contained login
				if(isset($_setting) && $allow) {
					# See if the page is an administration page
					$aPage		=	
					$is_admin	=	($this->getPageURI('is_admin') == 1);
					$username	=	$this->getPost('username');
					if(!empty($username)) {
						# See if there is a login bypass file
						$_bypass	=	$this->getBypass('login');
						$bfile		=	$this->safe()->normalize_url(NBR_ROOT_DIR.DS.$_bypass);
						$_bypass	=	(is_file($bfile));
						$lMsg		=	PHP_EOL.date('Y-m-d H:i:s')." TOKEN: [OK] USERNAME: [".$this->getPost("username")."] LINE: [".__LINE__."] CLASS: [".__CLASS__.']';
						$fName		=	array(
											"path"=>NBR_CLIENT_DIR.DS."settings".DS.'reporting'.DS."errorlogs".DS,
											"filename"=>"login.txt"
										);
						
						# If it's not an administration page and bypass is valid
						if($_bypass && !$is_admin) {
							# Save to log file the login attempt
							$this->saveToLogFile($fName,$lMsg,array('logging','login'),array('type'=>'c+'));
							include_once($bfile);
						}
						# Process the login normally
						else {
							# Check if the user is valid
							$user		=	$this->getUserInfo($this->getPost('username'));
							# If user invalid, return
							if(!$user) {
								$incOpts	=	array('error'=>'invalid username/password');
								# Save incidental
								$this->saveIncidental('login',$incOpts,true);
								//$this->saveToLogFile($fName,$lMsg.': INVALID LOGIN'.PHP_EOL,array('logging','write_type'=>false),array('type'=>'c+'));
								
								$this->getHelper('nLogger')
									->save(array(
										'TIMESTAMP'=>date('Y-m-d H:i:s'),
										'MESSAGE'=>'invalid username/password',
										'TOKEN'=>'OK',
										'USERNAME'=>$this->getPost('username'),
										'CLASS'=>__CLASS__,
										'METHOD'=>__FUNCTION__
										),
										$logFilePath,'a+');
								
								if($ajaxRespond) {
									if($this->isAjaxRequest())
										$this->ajaxResponse(array('alert'=>'Invalid Username/Password'));
								}
								
								return false;
							}						
							# If the referring page is not admin page
							if(!$aPage) {
								# If admin user valid
								if($this->isAdmin($user->usergroup)) {
									# Check if allow from any-page-admin-login is set
									$openAllow	=	true;
									# See if there is a define to keep it off
									if(defined("OPEN_ADMIN"))
										$openAllow	=	OPEN_ADMIN;
									# If not allowed to log in except for admin page stop
									if(!$openAllow) {
										# Browser-facing message
										$getCustMsg	=	$this->getMatchedArray(array('messaging','login','client','fail','admin'));
										# Get custom messaging
										$nMsg	=	(!empty($getCustMsg['admin'][0]))? $this->getHelper('nAutomator',$this)->matchFunction($getCustMsg['admin'][0]) : 'logging into admin area must be done through admin page.';
										# Set error into data array
										$msg	=	array('error'=>$nMsg);
										
										# Save to log
										$fName['filename']	=	'login_'.date('YmdHis').time().'.log';
										
										$this->getHelper('nLogger')
											->save(array(
												'TIMESTAMP'=>date('Y-m-d H:i:s'),
												'MESSAGE'=>'ERROR: Admin user login on non-admin page. You must log in using your admin page found at '.$this->adminUrl().' or switch your constant in your registry to "true": <open_admin>true</open_admin>',
												'TOKEN'=>'OK',
												'USERNAME'=>$this->getPost('username'),
												'CLASS'=>__CLASS__,
												'METHOD'=>__FUNCTION__
												),
												$logFilePath,'a+');
										
										if($this->isAjaxRequest())
											$this->ajaxResponse(array('alert'=>$msg));
								
										# Save incidental for browser alert
										$this->saveIncidental('login',$msg,true);
										$this->setSession('login',array('msg'=>$nMsg));
										# Stop action
										return false;
									}
								}
							}
							# Continue logging in
							$this->getHelper('processLogin')->execute($this->toArray($this->getRawPost()));
							$success	=	$this->getIncidental('login')->{0};
						}
					}
				}
				
				if(!$skipRedirect) {
					if(isset($success)) {
						# If there is a jumppage, execute it
						if($this->getPost('jumppage'))
							$this->processJumpPage();
						# If there is an auto-forward after loging on
						elseif(!empty($this->getPageURI('auto_fwd_post'))){
							# Get path
							$path	=	$this->getPageURI('auto_fwd');
							# If there is a path to auto-forward to
							if(!empty($path)) {
								if($path !== 'NULL') {
									# If there is no external path, just tack on the site url
									if(strpos($path,'http') !== true)
										$path	=	$this->localeUrl($path);
									# Ajax redirect
									$this->doAjaxRedirect($path);
									# Route to a new page
									$this->nRouter->addRedirect($path);
								}
							}
						}
						# Ajax redirect
						$this->doAjaxRedirect($redirect);
						# Redirect back to self
						$this->nRouter->addRedirect($redirect);
					}
				}
			}
		
		public	function loginRemotely()
			{
				$reload	=	false;
				$host	=	"http://www.nubersoft.com/api/index.php?service=Fetch.Account&action=login_remote&vals=".Safe::jSURL64($_POST);
				$cURL	=	new cURL($host);
				
				if(!empty($cURL->response['login']) && $cURL->response['login'] == 1) {
					$_SESSION['username']	=	$_POST['username'];
					$_SESSION['usergroup']	=	1;
					$_SESSION['first_name']	=	$_POST['username'];
					$reload					=	true;
				}
				
				return $reload;
			}
		
		/*
		**	@description	Takes a jump page, converts it, then redirects
		*/
		public	function processJumpPage($path=false)
			{
				if($this->getRequest('jumppage') || !empty($path)) {
					# Fetch the path
					$link	=	(!empty($path))? $path : $this->getJumpPage($this->getRequest('jumppage'));
					if(!preg_match('/^http/',$link))
						$link	=	$this->siteUrl("/".ltrim($link,'/'));
					# Redirect using ajax if requested
					$this->doAjaxRedirect($link);
					# Redirect if not ajax
					$this->nRouter->addRedirect($link);
				}
			}
		/*
		**	@description	Alias to nRouter method of the same name
		*/
		public	function doAjaxRedirect($path)
			{
				$this->getHelper('nRouter')->{__FUNCTION__}($path);
			}
		/*
		**	@description	Determines how to forward
		*/
		public	function redirectOnForward($force = false)
			{
				$getPage			=	$this->getPageURI();
				$isForwarded		=	$this->getPageURI('auto_fwd');
				$isForwardedAfter	=	($this->getPageURI('auto_fwd_post') == 'on');
				$hasExpired			=	$this->getIncidental('session_expired');
				# See if there is an expiration notice set
				if(!empty($hasExpired)) {
					# If the request is via ajax
					if($this->isAjaxRequest()) {
						# Just die and output the expire value
						die(json_encode(array(
							'html'=>array(
								'<script>alert("Session has expired."); window.location=\''.$this->siteUrl().'/?action=logout\';</script>'
							),
							'sendto'=>array(
								'body'
							)
						)));
					}
					else {
						$path	=	(!empty($isForwarded))? $isForwarded : $this->localeUrl();
						# Redirect to either home or to forwarded path
						$this->nRouter->addRedirect($path);
					}
				}
				else {
					if(!$force) {
						if($this->isAdmin())
							return;
					}
				}
				# If there is an autoforward, continue
				if(empty($isForwarded))
					return;
				# Create the path
				$path	=	(preg_match('/^http.*/',$isForwarded))? $isForwarded : $this->localeUrl($isForwarded);
				# If there is a forward after login
				if($isForwardedAfter) { 
					# Check if user is logged in
					if($this->isLoggedIn()) {
						# If logged in, stop if user is admin
						if($this->isAdmin())
							return;
					}
					else
						return;
				}
				# Redirect
				$this->nRouter->addRedirect($path);
			}
		/*
		**	@description	Determines how to forward on session expire
		*/
		public	function checkSessionExpire()
			{
				$session_expire	=	$this->getSessExpTime();
				if($this->nSessioner->isExpired($session_expire)){
					$this->nSessioner->destroy();
					$this->saveIncidental('session_expired',true);
				}
				else {
					$this->nSessioner->setExpired();
				}
			}
		
		public	function reportAjax()
			{
				if(!$this->isAjaxRequest())
					return;
				
				$errors			=	$this->toArray($this->getError('ajax'));
				$incidentals	=	$this->toArray($this->getIncidental('ajax'));
				$settings		=	$this->toArray($this->getDataNode('ajax'));
				$alert			=	$this->getMatchedArray(array('alert'),'',$incidentals);
				$report			=	array(
										'errors'=>$errors,
										'warnings'=>$incidentals,
										'settings'=>$settings,
										'request'=>$this->getRequest()
									);
	
				if(!empty($alert['alert'][0]))
					die(json_encode(array('alert'=>$alert['alert'][0],'data'=>$report)));
				else
					die(json_encode(array('data'=>$report)));
			}
		/*
		**	@description	Removes the cache contents
		*/
		public	function deleteCacheFolder($additionals = false)
			{
				# Get the cache engine
				$Cache	=	$this->getPlugin('\nPlugins\Nubersoft\Cache');
				# Start live cache bypass
				$Cache->setCacheBypass();
				# Pause so requests can stop accessing the cache files
				sleep(2);
				# Get the cache folder path
				$cache	=	$this->toSingleDs($this->getCacheFolder());
				$msg	=	array();
				if(is_array($additionals)) {
					foreach($additionals as $path) {
						$new	=	$this->toSingleDs(NBR_CLIENT_SETTINGS.DS.str_replace('/',DS,$path));
						if(is_file($new)) {
							$deleted[]	=	(@unlink($new))? array('file'=>$path,'success'=>'ok') : array('file'=>$path,'success'=>'error');
								
						}
						else
							$deleted[]	=	array('file'=>$path,'success'=>false);
						
						$msg[]	=	"File deleted (".trim($path,DS).")";
					}
				}
				
				if(!is_file($cache) && !is_dir($cache)) {
					$msg[]	=	'Cache folder does not exist';
					
					if($this->isAjaxRequest()) {
						$deleted['alert']	=	implode(', ',$msg);
						$this->saveIncidental('ajax',$deleted);
					}
					else
						$this->saveIncidental('cache_delete',implode(', ',$msg));
					# Remove the cache bypass
					$Cache->removeCacheBypass();
					return;
				}
				else {
					if(is_dir($cache)) {
						$tmp	=	NBR_CLIENT_SETTINGS.DS.date('Ymdhis').'cache_remove';
						rename($cache,$tmp);
						$cache	=	$tmp;
					}
				}
				
				$this->getHelper('nFileHandler')->deleteContents($cache);
				# Remove the cache bypass so the system can start cacheing again
				$Cache->removeCacheBypass();
				
				if(!is_dir($cache) && !is_file($cache)) {
					$msg[]	=	'Cache Folder Deleted (/'.pathinfo($cache, PATHINFO_BASENAME).')';
					
					if($this->isAjaxRequest()) {
						$resetDomThinker	=	$this->processResetThinkers();
						$deleted['alert']	=	implode(', ',$msg);
						$this->ajaxResponse(array_merge(array(
							'alert' => $deleted['alert'],
							'fx' => array(
								'rOpacity'
							),
							'acton' => array(
								'body'
							)
						),$resetDomThinker));
					}
					else
						$this->saveIncidental('cache_delete',implode(', ',$msg));
				}
				
				return $this;
			}
		
		public	function processResetThinkers()
			{
				$deliver	=	$this->toArray($this->getPost('deliver'));
				$node		=	(new \Nubersoft\Methodize())->saveAttr('deliver',$deliver);
				$dom		=	$node->getDeliver();
				
				if(!is_string($dom))
					$dom	=	$node->getUxLoaderReset()->toArray();
				
				return array(
					'html' => array_fill(0,count($dom),''),
					'sendto' => $dom
				);
			}
		
		public	function resetClientDefine()
			{
				$file	=	$this->toSingleDs($this->getCacheFolder().DS.'config-client.php');
				$event	=	($this->isAjaxRequest())? 'ajax' : 'event';
				$msg	=	array(
								'name'=>__FUNCTION__,
								'action'=>$this->getRequest('action')
							);
				
				if(is_file($file)) {
					if(!unlink($file)) {
						$this->saveIncidental($event,array_merge(array(
							'success'=>false,
							'alert'=>'Define could not be deleted'
						),$msg));
						
						return $this;
					}
					else {
						$this->saveIncidental($event,array_merge(array(
							'success'=>true,
							'alert'=>'Define reset'
						),$msg));
						$this->setClientDefine();
						
						return $this;
					}
				}
				else {
					$this->saveIncidental($event,array_merge(array(
						'success'=>false,
						'alert'=>'No define to remove'
					),$msg));
				}
				
				return $this;
			}
		/*
		**	Checks the preference for forcing page to run to ssl
		*/
		public	function redirectOnSsl($force = false,$port = 80)
			{
				if(empty($force))
					$force	=	(defined("FORCE_URL_SSL"))? FORCE_URL_SSL : false;
				
				if($_SERVER['SERVER_PORT'] == $port) {
					if($force) {
						if(empty($this->isSsl()))
							$this->nRouter->addRedirect($this->siteUrl($_SERVER['REQUEST_URI'],true));
					}
				}
			}
	}