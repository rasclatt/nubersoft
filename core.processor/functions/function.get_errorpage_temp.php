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
		$default	=	$use	=	NBR_TEMPLATE_DIR._DS_."default"._DS_."site.live.php";
		$template	=	(!empty($settings['site_live']))? $settings['site_live']:false;
		$useTemp	=	(!empty(\nApp::getDataNode('page_prefs')->template))?  \nApp::getDataNode('page_prefs')->template : false;
		
		if($template != false && is_file(NBR_ROOT_DIR.$template))
			$use = NBR_ROOT_DIR.$template;
		else {
			if($useTemp != false) {
				if(is_file($temp_dir = str_replace(_DS_._DS_,_DS_,NBR_ROOT_DIR._DS_.\nApp::getDataNode('page_prefs')->template._DS_."site.live.php")))
					$use	=	$temp_dir;
			}
		}
					
		include($use);
	}