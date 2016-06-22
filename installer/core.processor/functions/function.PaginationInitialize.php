<?php
	function PaginationInitialize($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('check_empty');
			// Set default Table search
			$table				=	(!isset($settings['table']))? 'components' : $settings['table'];
			// Set if admin can only check this table
			$admin				=	(isset($settings['admin']) && ($settings['admin'] == 'true' || $settings['admin'] == true || $settings['admin'] == 1 || $settings['admin'] == '1'))? true: false;
			// Set the amount of before and after pages relative to current
			// Example: Set to two, would be two max numbers before and after (3 being current):   << 1 2 3 4 5 >>
			$spread				=	(!empty($settings['spread']))? (is_numeric($settings['spread']))?$settings['spread']:4:4;
			// Create instance of Search Engine
			$search				=	new SearchEngine($table,$admin);
			// Apply settings
			$array				=	$search->fetch($settings);
			// Save data to arrays
			$SearchEngine['data']		=	($array->stats != false)? $array->stats:array();
			$SearchEngine['columns']	=	(isset($array->columns) && is_array($array->columns))? $array->columns:false;
			// Create instance of global variablizer
			$register			=	new RegisterSetting();
			// Save settings to global static
			$register->UseData('pagination',$SearchEngine)->SaveTo('settings');
			
			if(check_empty($settings,'return',true))
				return	$SearchEngine;
		}
?>