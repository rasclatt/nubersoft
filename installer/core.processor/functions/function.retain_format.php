<?php
	function retain_format($settings = false)
		{
			register_use(__FUNCTION__);
			if(isset($settings['content']) && !empty($settings['content'])) {
					ob_start();
					if(isset($settings['quotes']) && $settings['quotes'] == true)
						echo htmlentities($settings['content'],ENT_QUOTES,'UTF-8');
					else
						echo htmlentities($settings['content'],'UTF-8');
						
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
		}
?>