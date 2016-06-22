<?php
	function menu_get_all()
		{
			register_use(__FUNCTION__);
			
			AutoloadFunction("nQuery,organize");
			$nubquery		=	nQuery();
			
			if($nubquery == false)
				return;
			
			$menus	=	$nubquery	->select(array("ID","unique_id","parent_id","link","menu_name","in_menubar","full_path","page_live"))
									->from("main_menus")
									->orderBy(array("page_order"=>"ASC"))
									->fetch();
			
			return $menus;
		}
?>