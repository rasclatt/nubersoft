<?php
/*Title: get_table_columns()*/
/*Description: This function will fetch all the table information from the database.*/
	function get_table_columns($table = false,$alldata = false)
		{
			
			if(empty($table))
				return false;
			
			$newCols	=	array();
			
			AutoloadFunction('nQuery');
			// Fetch columns in table
			$columns	=	nQuery()->describe($table)->fetch();
			
			if(!$alldata && is_array($columns)) {
					// Loop results, store column name
					foreach($columns as $cols) {
							$newCols[]	=	$cols['Field'];
						}
				}
			else {
			 		AutoloadFunction('organize');
					$newCols	=	organize($columns, 'Field');				
				}
				
			return $newCols;
		}
?>