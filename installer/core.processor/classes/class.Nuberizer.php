<?php
	class Nuberizer
		{
			protected	$autoloader;
			protected	$head;
			protected	$mast;
			protected	$menu;
			protected	$login;
			protected	$body;
			protected	$foot;
			protected	$page;
			protected	$login_valid;
			protected	$page_view;
			protected	$timezone;
			protected	$TimeZone;
			protected	$layout;
			protected	$error;
			
			public	function __construct()
				{
					AutoloadFunction('display_autoloaded');
				}
			
			public	function Core()
				{
					// Autoload functions
					AutoLoadFunction('autoload_core_functions');
					// Autoload function
					autoload_core_functions('nuber');
					// This will verify there is an htaccess file in the root and create if not
					$this->validateReWriteEngine();
					// Fetch Config Files (XML)
					$this->getAllConfigs();
					// Create Menu Data
					$this->InitializeMenuSet();
					// Process Database hits and login attempts
					$this->processRequests();
					// Toggle Edit Mode
					$this->toggleEditMode();
					// Assign template type->User assigned or admintools
					$this->fetchTemplate();
					// Check Database NOT ******* SURE USE HERE *******
					// get_now_status();  *****************************
					// Create the timestamping for logged in users
					$this->timeStampUse();
					// Get site options like status, timezone, etc.
					$this->LoadSiteSettings();
					// If Auto-forward is set, process it
					// This function will exit it autoforward true
					$this->AutoForwardRequests();
					// Apply site-live
					if(!nApp::siteLive() && !is_admin()) {
						// This will overwrite the site live message with the not found message
						// When javascript tries to pull a page silently that doesn't exist, if a fetch_token()
						// is being called, it will reset that token by reloading the page.
						// In this case, we want to show the not-found page rather than the site offline page.
						silent_error();
						if(!empty(nApp::getErrorTemplate())) {
							AutoloadFunction('render_error');
							echo render_error();
						}
						else {
							AutoloadFunction('get_errorpage_temp');
							echo get_errorpage_temp();
						}
					}
					else {
						// Double check there is a config file
						$this->checkDBConnect();
						
						$this->page	=	nApp::getPage();
						$value		=	(!empty(nApp::getPage('unique_id')))? nApp::getPage('unique_id') : false;
						nApp::saveSetting('site',array('unique_id'=>$value));
						// Grab the proper template
						$template	=	nApp::getSite('template');
						if(is_string($template))
							include($template);
					}
				}
			
			protected	function checkDBConnect()
				{
					if(!is_file(NBR_CLIENT_DIR.'/settings/dbcreds.php'))
						die(nApp::getErrorLayout('dbcreds'));
				}
			
			protected	function getConfig($pDir,$set = 'plugin')
				{
					AutoloadFunction("get_directory_list");
					if(!is_dir($pDir))
						return false;
					
					$dir	=	get_directory_list(array("dir"=>$pDir,"type"=>array("xml")));
					
					$setRightArr	=	function($array) {
						if(isset($array['onprocessheader']))
							return array('onprocessheader','header');
					};
					
					if(!empty($dir['host'])) {
						// Count how many files to process
						$cConfig		=	count($dir['host']);
						// Loop through configs
						for($i = 0; $i < $cConfig; $i++) {
							// Try and fetch the array from xml
							$cArr		=	nApp::getRegistry($dir['host'][$i]);
							// Anon function to check for key instruction
							$cArrKey	=	$setRightArr($cArr);
							// If no instructions set skip rest
							if(empty($cArrKey))
								continue;
							// If there is a proper instruction, assign the array
							$arr		=	$cArr[$cArrKey[0]];
							// If no file path is indicated, skip rest
							if(empty($arr['use']))
								continue;
							// Set the requester (post,get,request)
							$requester	=	(!empty($arr['requester']))? $arr['requester'] : 'post';
							// Set the action, if no action, it will just include
							$action		=	(!empty($arr['action']))? $arr['action'] : false;
							// Create a load array
							$this->autoloader[$cArrKey[1]][$requester][]	=	array("action"=>$action,"include"=>$arr['use']);
						}
					}
					
					return $this;
				}
				
			protected	function autoLoad($type = 'header')
				{
					$types[]	=	'post';
					$types[]	=	'get';
					$types[]	=	'request';
					
					if(isset($this->autoloader[$type])) {
						for($i = 0; $i < 3; $i++) {
							if(empty($this->autoloader[$type][$types[$i]]))
								continue;

							$array	=	$this->autoloader[$type][$types[$i]];
							$count	=	count($array);
							
							for($a = 0; $a < $count; $a++) {
								$func	=	false;
								$inc	=	false;
								if(empty($array[$a]['action']))
									$inc	=	true;
								else {
									switch($types[$i]) {
										case('post'):
											if(nApp::getPost('action') == $array[$a]['action']) {
										 		$inc	=	true;
												break;
											}
										case('request'):
											if(nApp::getRequest('action') == $array[$a]['action']) {
										 		$inc	=	true;
												break;
											}
										case('get'):
											if(nApp::getGet('action') == $array[$a]['action']) {
										 		$inc	=	true;
												break;
											}
									}
								}
								
								if($inc) {
									$func	=	preg_replace('/(function|class).(.*)/', "$2",pathinfo($array[$a]['include'],PATHINFO_FILENAME));
									if(!function_exists($func)) {
										if(is_file(NBR_ROOT_DIR.$array[$a]['include']))
											include(NBR_ROOT_DIR.$array[$a]['include']);
										else
											RegistryEngine::saveError('nXmLoader',"BAD PATH: ".$array[$a]['include']);
									}
										
									if(function_exists($func))
										$func();
									else
										RegistryEngine::saveError('nXmLoader',"FUNCTION INACCESSABLE: {$func}()");
								}
							}
						}
					}
				}
			
			protected	function InitializeMenuSet()
				{
					$menus		=	new MenuEngine();
					$menus->FetchMenuData();
					
					return $this;
				}
			
			protected	function processRequests()
				{
					// run autoloaded xml-based functions
					$this->autoLoad('header');
					process_requests();
					
					if(nApp::getPost('update') && nApp::getPost('requestTable')) {
						if(nApp::getPost()->requestTable !== 'system_settings')
							return;

						header("Location: ".$_SERVER['HTTP_REFERER']);
						exit;
					}
				}
			
			protected	function toggleEditMode()
				{
					register_use(__METHOD__);					
					initialize_edit_mode();
				}
				
			protected	function AutoForwardRequests()
				{
					register_use(__METHOD__);
					
					AutoForward();
				}
				
			protected	function fetchTemplate()
				{
					$page	=	nApp::getPage();
					$site	=	nApp::getSite();
					
					if(empty($page->unique_id)) {
						$admin		=	false;
						$template	=	false;
					}
					// Set admintools template
					elseif(is_admin() && !nApp::siteValid()) {
						$template	=	false;
						$admin		=	true;
					}
					elseif(!isset($page->template)) {
						$template	=	false;
						$admin		=	nApp::siteValid();
					}
					elseif(isset($page->is_admin) && $page->is_admin == 1) {
						$admin		=	true;
						$template	=	(!empty($page->template))? NBR_ROOT_DIR."/".$page->template : false;
					}
					else {
						$admin		=	false;
						$template	=	(isset($page->template))? NBR_ROOT_DIR."/".$page->template : false;
					}
					
					// Send 404 error
					if(empty($page->unique_id) || !$pLive)
						header('http/1.1 404 not found');
						
					// Return a template directory
					if(!empty($site->template))
						$site->template	=	get_template($template,$admin);
					else
						RegistryEngine::saveSetting(array("use"=>"site","data"=>array("template"=>get_template($template,$admin))));
					
				}
				
			protected	function timeStampUse()
				{
					get_timestamp();
				}
			
			protected	function LoadSiteSettings()
				{
					nApp::saveSetting('system', array("site"=>get_site_options()));
				}
			
			protected	function validateReWriteEngine()
				{
					// If the main htaccess is not found
					if(!is_file(NBR_ROOT_DIR.'/.htaccess')) {
						// Autoload functions to create
						AutoloadFunction("get_default_htaccess,get_site_prefs");
						// Try and get a database-saved version
						$getprefs	=	nApp::getSitePrefs();
						// If there is one, create from that
						if(!empty($getprefs->site->content->htaccess))
							get_default_htaccess(array('htaccess'=>$getprefs->site->content->htaccess,'write'=>true));
						// Create default htaccess file
						else
							get_default_htaccess(array('write'=>true));
					}	
				}
			
			protected	function getAllConfigs($additional = false)
				{
					// Common places to find config.xml files
					$locations[]	=	NBR_ROOT_DIR.'/plugins/';
					$locations[]	=	NBR_CLIENT_DIR.'/plugins/';
					$locations[]	=	NBR_CLIENT_DIR.'/apps/';
					
					foreach($locations as $config) {
						// Autoload plugins from xml
						if(is_dir($config))
							$this->getConfig($config);
					}
				}
		}