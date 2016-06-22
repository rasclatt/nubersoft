<?php
	function get_page_title($default = false)
		{
			$default	=	(!empty($default))? $default : trim(NubeData::$settings->page_prefs->menu_name);
			if(!empty($default))
				return $default;

			if(empty(NubeData::$settings->page_prefs->ID))
				return "Whoops! Wrong Page";
			
			return $_SERVER['HTTP_HOST'];
		}
?>