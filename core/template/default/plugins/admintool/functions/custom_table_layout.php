<?php

function CheckCustomTbl($table = false,$root)
	{
		$custom	=	$root.'/form.'.$table.'.php';
		return (is_file($custom))? $custom:false;
	}

function custom_table_layout($table = false,$values = array(),$columns = array(),$settings = array(),$dropdowns = array(), $nuber = false)
	{
		
		// $values		=	data for the table
		// $columns		=	columns for the table
		// $select		=	array with select options
		// $dropdowns	=	array with dropdowns array
		// $nuber		=	main engine object	
		
		$client	=	CheckCustomTbl($table,NBR_CLIENT_DIR.'/settings/engine/admintools');
		$tbl	=	CheckCustomTbl($table,NBR_ROOT_DIR.'/core/engine/admintools');
		
		if($tbl != false) {
				include($tbl);
				return true;
			}
		elseif($client != false) {
				include($client);
				return true;
			}
		else
			return false;
	}