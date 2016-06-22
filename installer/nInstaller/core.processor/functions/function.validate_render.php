<?php
	function validate_render()
		{
			register_use(__FUNCTION__);
			AutoloadFunction('site_valid');
			$validate[]		=	((defined(ROOT_DIR) && is_dir(ROOT_DIR)))? 0:1;
			$validate[]		=	(nApp::siteValid() == false)? 0:1;
			
			return (array_sum($validate) != 2)? false : true;
		}
?>