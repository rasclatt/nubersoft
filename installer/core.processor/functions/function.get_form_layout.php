<?php
/*Title: get_form_layout()*/
/*Description: This function retrieves the the designation of form fields for a certain group in the `form_builder` table.*/
	function get_form_layout($element = array())
		{
			register_use(__FUNCTION__);
			AutoloadFunction('convert_style,check_empty,nQuery');
			$nubquery			=	nQuery();
			$element["select"]	=	(!isset($element["select"]))? "component_value":$element["select"];
			
			$array	=	$nubquery->select($element["select"])->from("component_builder");
			
			if(!empty($element["where"]))
				$array->where($element["where"]);
				
			$data	=	$array->fetch();
			
			return $data;
		}
?>