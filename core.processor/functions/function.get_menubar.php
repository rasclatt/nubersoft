<?php
/*Title: get_menubar()*/
/*Description: This function captures the main menu and renders it.*/
	function get_menubar($local = false)
		{
			if(empty($local))
				$bypass	=	(!isset(NubeData::$settings->bypass->menu))? false : NubeData::$settings->bypass->menu;
			else
				$bypass	=	$local;
				
			$menus	=	new MenuButton();
			ob_start();
			echo $menus->FetchSub()->GraphicMenu($bypass)->thislayout;
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}