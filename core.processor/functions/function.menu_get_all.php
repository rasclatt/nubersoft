<?php
	function menu_get_all()
		{
			AutoloadFunction("nQuery,organize");
			if(!nQuery())
				return;
			
			
			
			$menus	=	nApp::getAllMenus();
			
			return $menus;
		}