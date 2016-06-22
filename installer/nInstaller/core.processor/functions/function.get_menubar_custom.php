<?php
/*Title: get_menubar_custom()*/
/*Description: This function allows for bypassing the main menu*/
	function get_menubar_custom($layout = false)
		{
			register_use(__FUNCTION__);
			$bypass	=	(!isset(NubeData::$settings->bypass->menu))? false : NubeData::$settings->bypass->menu;
			$menus	=	new MenuButton();

			if(!empty($layout))
				$menus->UseLayout(array('layout'=>$layout));
			
			ob_start();
			echo $menus->GraphicMenu($bypass,$menus->FetchSub())->thislayout;
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>