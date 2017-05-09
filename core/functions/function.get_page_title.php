<?php
function get_page_title($default = false)
	{
		$name		=	\nApp::getPage('menu_name');
		$default	=	(!empty($default))? $default : trim($name);
		
		if(!empty($default))
			return $default;

		if(empty(\nApp::getPage('ID')))
			return "Whoops! Wrong Page";
		
		return $_SERVER['HTTP_HOST'];
	}