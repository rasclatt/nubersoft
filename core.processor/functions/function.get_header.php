<?php
/*Title: get_header()*/
/*Description: This function renders the `<head>` information to the page using either manual input, a trace callback to search for inclusion file in calling-function directory, or lastly default `<head>`.*/
	function get_header($settings = false)
		{
			
			AutoloadFunction('get_site_options,get_header_options,check_empty,nuber_faux');
			// Check if there is any raw text
			$html				=	(!empty($settings['html']))? $settings['html']:false;
			
			// Skip the header 
			$prefs['head']		=	(isset($settings['head']))? $settings['head']:true;
			$prefs['page']		=	nApp::getPage();
			$prefs['site']		=	(!empty(NubeData::$settings->preferences->site->content))? NubeData::$settings->preferences->site->content: get_site_options();
			$prefs['header']	=	(!empty(NubeData::$settings->preferences->header->content))? NubeData::$settings->preferences->header->content: get_header_options();
			
			ob_start();
			if(!$html) {
					if(!empty($settings['link'])) {
							if(is_file($settings['link']))
								$link = $settings['link'];
						}
						
					if(empty($link)) {
							$backtrace	=	debug_backtrace();
							$current	=	(isset($backtrace[0]['file']))? explode("/",$backtrace[0]['file']) : NubeData::$settings->site->template_head;
							if(is_array($current)) {
									$current	=	array_filter($current);
									array_pop($current);	
									$current	=	"/".implode("/",$current)."/";
								}
								
							unset($backtrace);
							$template	=	Safe::normalize_url($current.'/head.php');
							$default	=	Safe::normalize_url(NubeData::$settings->site->template_head.'/head.php');
							$link		=	(is_file($template))? $template : $default;
						}
					
					$access		=	true;
					include_once($link);
				}
			else {
					AutoloadFunction('use_markup');
					echo use_markup($html);
					return;
				}
				
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}
?>