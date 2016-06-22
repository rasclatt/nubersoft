<?php
/*Title: get_errorpage_temp()*/
/*Description: This function will attempt to display the site disabled page.*/

	function get_errorpage_temp($settings = false)
		{
			// Check for a defined contant
			if(defined("SITE_LIVE") && is_file(SITE_LIVE)) {
					include(SITE_LIVE);
					return;
				}
				
			// Load checker
			AutoloadFunction('check_empty');
			// Set default
			$default	=	$use	=	TEMPLATE_DIR."/default/site.live.php";
			$template	=	(!empty($settings['site_live']))? $settings['site_live']:false;
			$useTemp	=	(!empty(NubeData::$settings->page_prefs->template))? NubeData::$settings->page_prefs->template:false;
			
			if($template != false && is_file(ROOT_DIR.$template))
				$use = ROOT_DIR.$template;
			else {
					if($useTemp != false) {
							if(is_file($temp_dir = str_replace("//","/",ROOT_DIR."/".NubeData::$settings->page_prefs->template."/site.live.php")))
								$use	=	$temp_dir;
						}
				}
						
			include($use);
		}
?>