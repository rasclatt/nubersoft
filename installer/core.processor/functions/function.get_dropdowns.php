<?php
	function get_dropdowns($table = false)
		{	
			register_use(__FUNCTION__);
			
			if($table == false)
				return false;
			
			AutoloadFunction('get_table_columns,nQuery,check_empty,organize');
			// Get query engine
			$nubquery	=	nQuery();
			// Fetch columns in table
			$newCols	=	get_table_columns($table);
			try {
			// Check if stored dropdown settings
			$fields		=	$nubquery	->select(array("assoc_column","menuName","menuVal","page_live"))
										->from("dropdown_menus")
										->wherein("assoc_column",$newCols)
										->orderBy(array("page_order"=>"ASC"))
										->Record(__FILE__)
										->fetch();
				}
			catch (Exception $e){
					AutoloadFunction("create_default_menus");
					create_default_menus();
				}
			return organize($fields,'assoc_column');
		}
?>