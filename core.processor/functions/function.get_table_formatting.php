<?php

	function get_table_formatting($settings = false)
		{
			
			AutoloadFunction("get_table_columns");
			
			$column		=	(!empty($settings['column']))? $settings['column']:false;
			$table		=	(!empty($settings['table']))? $settings['table']:false;
			// Get columns from the table
			$columns	=	get_table_columns($table);
			// Check if there are any special form options
			$formatted	=	organize(nQuery()	->select(array("column_name","column_type","size"))
												->from("form_builder")
												->wherein("column_name",$columns)
												->fetch(),"column_name");
			// If there are no columns in table return false
			if(empty($columns))
				return false;
			// If there are no special columns, return columns
			if($formatted == 0)
				return $column;
			// Get the difference between total cols vs formatted cols
			$diff		=	array_diff($columns,array_keys($formatted));
			// Create a blank array to return same-result with blank as filled array
			$blank		=	array_fill(0,count($diff),array("column_name"=>"","column_type"=>"text","size"=>"100%"));
			// Merge the blank with the filled
			$arr		=	array_merge($formatted,array_combine($diff,$blank));
			// Sort the array
			ksort($arr);
			// Rerturn final array
			return 		$arr;	
		}
?>