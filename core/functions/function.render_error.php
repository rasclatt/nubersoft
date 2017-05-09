<?php
	function render_error($settings = array())
		{
			 
			$settings['display']	=	(isset($settings['display']))? $settings['display']:NBR_ROOT_DIR.'/core/template/default/site.error404.php';
			$settings['msg']		=	(isset($settings['msg']))? $settings['msg']:array("title"=>"Whoops! Page not found.","body"=>"It's possible the page you are looking for has been moved or removed.");
			
			if(silent_error()) {
					ob_start();
					echo core::WrongPage($settings['display'],$settings['msg']);
					
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
		}
?>