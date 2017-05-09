<?php
function fetch_component_options($comp_settings = false)
	{
		$table	=	(!empty($comp_settings['table']))? $comp_settings['table'] : "menu_display";
		$pId	=	(!empty($comp_settings['parent_id']))? $comp_settings['parent_id'] : false;
		
		$values	=	nQuery()	->select()
								->from("menu_display")
								->where(array("parent_id"=>$pId))
								->fetch();

		$values	=	($values != 0)? $values[0]:false;
		if(!empty($values['options'])) {
				$options	=	json_decode($values['options'],true);
				
				foreach($options as $key => $value) {
						$values['options['.$key.']']	=	$value;
					}
			}
			
		AutoloadFunction("component_assembler,form_field");
		$settings	=	component_assembler(array("table"=>Safe::URL64($table),"format"=> "menu_display"));
		$layout		=	(!empty($settings['layout']))? $settings['layout']:array();
		$array		=	(!empty($settings['format']))? $settings['format']:array();
		$options	=	(!empty($settings['options']))? $settings['options']:array();
		$title		=	($values)? substr(Safe::decodeForm($values['content']),0,20) : "Create New";
		$function	=	(!empty($values['ID']))? "update":"add";
		
		$useArray['layout']		=	$layout;
		$useArray['array']		=	$array;
		$useArray['options']	=	$options;
		$useArray['title']		=	$title;
		$useArray['function']	=	$function;
		$useArray['settings']	=	$settings;
		$useArray['values']		=	$values;
		
		return $useArray;
	}