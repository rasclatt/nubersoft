<?php
/*Title: get_template()*/
/*Description: Determines which page template to use.*/
	function get_template($template = false,$admin = false,$check = false)
		{
			
			$payload			=	(!empty(nApp::getPage()))? nApp::getPage() : Safe::to_object(array());
			$site_tFolder		=	NBR_TEMPLATE_DIR.DS.'default';
			$default_tFolder	=	(!empty(nApp::getSiteContent()->template_folder))? NBR_ROOT_DIR.nApp::getSiteContent()->template_folder : $site_tFolder;
			// If template not empty, use that or else use default dir
			$template_dir		=	(empty($default_tFolder))? $template : $default_tFolder;
			// Assign page names
			// Default load page
			$page['temp']		=	DS.'include.php';
			// Admintools page
			$page['admin']		=	DS.'admintools.php';
			// Offline page
			$page['offline']	=	DS.'site.live.php';
			// Login page
			$page['login']		=	DS.'site.login.php';
			// If sub template set, assign
			$page['use']		=	(!empty($payload->use_page))? $payload->use_page : false;
			
			// Return if subpage set and is in place
			if(!empty($page['use']) && is_file($gopage = str_replace(DS.DS,DS,$template_dir.DS.$page['use'])))
				return $gopage;
				
			// See if page live
			$pLive	=	(!empty(nApp::getPage('page_live')) && nApp::getPage('page_live') == 'on');
		
			if(empty($payload->unique_id) || (!$pLive && !is_admin())) {
				if(is_file($useErr = $default_tFolder.DS."site.error404.php")) {
					if(!empty(NubeData::$settings->site->error_404))
						NubeData::$settings->site->error_404	=	$useErr;
					else
						RegistryEngine::saveSetting(array("use"=>"site","data"=>array("error_404"=>$useErr)));
					
					return $useErr;
				}
				else
					return $site_tFolder.DS.'site.error404.php';
			}
		
			if(!empty($payload->session_status)) {
				if($payload->session_status == 'on') {
					$usegroup	=	(!empty($payload->usergroup) && is_numeric($payload->usergroup))? $payload->usergroup : 3;
					
					if(empty(NubeData::$settings->user->loggedin)) {
						$gopage	=	(is_file($loginpg = str_replace(DS.DS,DS,$template_dir.DS.$page['login'])))? $loginpg : $site_tFolder.$page['temp'];
						return $gopage;
					}
				}
			}
			
			// Determine which type of page to return
			if($admin)
				$gopage = (is_file($usefile = $template_dir.DS.$page['admin']))? $usefile : $site_tFolder.DS.$page['admin'];
			else
				$gopage = (is_file($usefile = $template_dir.DS.$page['temp']))? $usefile : $site_tFolder.DS.$page['temp'];
			// Return
			return str_replace(DS.DS,DS,$gopage);
		}