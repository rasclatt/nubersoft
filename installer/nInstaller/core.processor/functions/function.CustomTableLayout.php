<?php
	
	function CheckCustomTbl($table = false,$root)
		{
			register_use(__FUNCTION__);
			$custom	=	$root.'/form.'.$table.'.php';
			return (is_file($custom))? $custom:false;
		}
	
	function CustomTableLayout($table = false,$values = array(),$columns = array(),$settings = array(),$dropdowns = array(), $nuber = false)
		{
			register_use(__FUNCTION__);
			// $values		=	data for the table
			// $columns		=	columns for the table
			// $select		=	array with select options
			// $dropdowns	=	array with dropdowns array
			// $nuber		=	main engine object	
			
			$client	=	CheckCustomTbl($table,CLIENT_DIR.'/settings/engine/admintools');
			$tbl	=	CheckCustomTbl($table,ROOT_DIR.'/core.processor/engine/admintools');
			
			
			
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
?>