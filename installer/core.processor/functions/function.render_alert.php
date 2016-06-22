<?php
	function render_alert($layout = false,$settings = array())
		{
			register_use(__FUNCTION__);
			if(!empty($settings) && $layout != false) {
					ob_start();
					if(is_file($layout))
						include($layout);
					else
						echo $layout;

					$data	=	ob_get_contents();
					ob_end_clean();
					// Replace Markup
					foreach($settings as $keys => $values) {
							$data	=	preg_replace('/\{'.$keys.'\}/',$values,$data);
						}
						
					return (isset($data))? $data:false;
				}
		}
?>