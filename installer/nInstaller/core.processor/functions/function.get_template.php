<?php
/*Title: get_template()*/
/*Description: Determines which page template to use.*/
	function get_template($template = false,$admin = false,$check = false)
		{
			register_use(__FUNCTION__);
			$payload			=	(!empty(NubeData::$settings->page_prefs))? NubeData::$settings->page_prefs : new StdClass();
			$site_tFolder		=	TEMPLATE_DIR.'/default';
			$default_tFolder	=	(!empty(NubeData::$settings->preferences->site->content->template_folder))? ROOT_DIR.NubeData::$settings->preferences->site->content->template_folder : $site_tFolder;
			// If template not empty, use that or else use default dir
			$template_dir		=	($template != false && is_dir($template))? $template : $default_tFolder;
			// Assign page names
			// Default load page
			$page['temp']		=	'/include.php';
			// Admintools page
			$page['admin']		=	'/admintools.php';
			// Offline page
			$page['offline']	=	'/site.live.php';
			// Login page
			$page['login']		=	'/site.login.php';
			// If sub template set, assign
			$page['use']		=	(!empty($payload->use_page))? $payload->use_page : false;
			
			// Return if subpage set and is in place
			if(!empty($page['use']) && is_file($gopage = str_replace("//","/",$template_dir."/".$page['use'])))
				return $gopage;

			if(empty($payload->unique_id)) {
					if(is_file($useErr = $default_tFolder."/site.error404.php")) {
							if(!empty(NubeData::$settings->site->error_404))
								NubeData::$settings->site->error_404	=	$useErr;
							else
								RegistryEngine::saveSetting(array("use"=>"site","data"=>array("error_404"=>$useErr)));
							
							return $useErr;
						}
					else
						return $site_tFolder.'/site.error404.php';
				}
			
			if(!empty($payload->session_status)) {
					if($payload->session_status == 'on') {
							$usegroup	=	(!empty($payload->usergroup) && is_numeric($payload->usergroup))? $payload->usergroup : 3;
							
							if(empty(NubeData::$settings->user->loggedin)) {
									$gopage	=	(is_file($loginpg = str_replace("//","/",$template_dir."/".$page['login'])))? $loginpg : $site_tFolder.$page['temp'];
									return $gopage;
								}
						}
				}
			
			// Determine which type of page to return
			if($admin)
				$gopage = (is_file($usefile = $template_dir."/".$page['admin']))? $usefile : $site_tFolder."/".$page['admin'];
			else
				$gopage = (is_file($usefile = $template_dir."/".$page['temp']))? $usefile : $site_tFolder."/".$page['temp'];
			// Return
			return str_replace("//","/",$gopage);
		} 
?>