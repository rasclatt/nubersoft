<?php
	function render_footer($file = false)
		{
			ob_start();
			
			if($file != false && is_file($file)) {
				include_once($file);
			}
			else {
				$bypass		=	nApp::getBypass('foot');
				if($bypass != false) {
					if(is_file($include = NBR_CLIENT_DIR.$bypass))
						include_once($include);
				}
				else {
					$footprefs				=	nApp::getFooterContent('html');
					$settings['content']	=	(isset($footprefs->value))? $footprefs->value : false;
					$settings['toggle']		=	(isset($footprefs->toggle))? $footprefs->toggle : false;
					$settings['bypass']		=	$bypass;							
					core::Footer($settings);
				}
			}
				
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}