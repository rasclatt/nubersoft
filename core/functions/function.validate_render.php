<?php
	function validate_render()
		{
			
			AutoloadFunction('site_valid');
			$validate[]		=	((defined(NBR_ROOT_DIR) && is_dir(NBR_ROOT_DIR)))? 0:1;
			$validate[]		=	(nApp::siteValid() == false)? 0:1;
			
			return (array_sum($validate) != 2)? false : true;
		}
?>