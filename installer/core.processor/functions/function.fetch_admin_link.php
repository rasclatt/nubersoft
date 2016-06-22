<?php
	function fetch_admin_link($return = false)
		{
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			
			if(!$nubquery)
				return;
				
			$admin		=	$nubquery	->select(array("menu_name","link","full_path"))
										->from("main_menus")
										->where(array("is_admin"=>1))
										->fetch();
										
			return	($admin != 0)? (($return)? $admin[0]:$admin[0]['full_path']):false;
		}
?>