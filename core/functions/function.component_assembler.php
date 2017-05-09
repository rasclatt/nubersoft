<?php
	function component_assembler($settings = false)
		{
			$table		=	(!empty($settings['table']))? Safe::URL64_decode($settings['table']):false;
			$formatting	=	(!empty($settings['format']))? $settings['format']:"components";
			AutoloadFunction("get_table_formatting");
			// Fetch the values from the query
			$query				=	nQuery();
			// Fetch the formatting for the table columns
			$result['format']	=	get_table_formatting(array("table"=>$formatting));
			if(empty($result['format']))
				return false;
			$columns			=	array_keys($result['format']);
			// Fetch the drop downs pertaining to the table columns
			$options			=	organize($query	->select(array("assoc_column","menuName","menuVal"))
													->from("dropdown_menus")
													->wherein("assoc_column",$columns)
													->orderBy(array("page_order"=>"ASC"))
													->fetch(),"assoc_column",true);
													
			$layout				=	organize($query	->select(array("component_value",'variable_type'))
													->from("component_builder")
													->where(array("assoc_table"=>$table,"page_live"=>"on"))
													->orderBy(array("page_order"=>"ASC","component_value"=>"ASC"))
													->fetch(),'variable_type',true);
			
			if(is_array($layout))
				ksort($layout);
			
			$result['layout']	=	$layout;
			$result['options']	=	$options;

			return $result;
		}
?>