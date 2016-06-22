<?php
	function validate_table_column($table = false,$cols = false,$values = false,$settings = false)
		{
			
			
			if(empty($table))
				return;
			
			$offset		=	(isset($settings['offset']))? $settings['offset'] : 0;
			$forceblank	=	(isset($settings['forceblank']))? $settings['forceblank'] : false;
			$combine	=	(check_empty($settings,'comb',true));
			
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			$query		=	$nubquery->addCustom("describe `".$table."`",true)->fetch();
			
			if($query == 0)
				return;
				
			if($values == false) {
				$values	=	$cols;
				$cols	=	array_keys($values);	
			}
			else
				$values	=	array_combine($cols,$values);
			
			$i = 0;
			
			foreach($query as $array) {
				$key			=	$array['Field'];
				if(!in_array($key,$cols))
					continue;
				
				// If the column is not an auto incremented column
				$allow[1]		=	(isset($array['Extra']) && $array['Extra'] == 'auto_increment');
				// List column names that are not allowed to be null
				$null_allowed	=	true;
				if(isset($array['Null']) &&  strtolower($array['Null']) == 'no')
					$null_allowed	=	false;
				
				// If autoincrement is false
				if($allow[1] == false) {
					// If empty is allowed in the field
					if($null_allowed) {
						// If the key/value is set
						if(isset($values[$key])) {
							// If the value is not empty OR is empty but allowed to be forced blank
							if(!empty($values[$key]) || $forceblank == true || $forceblank == 1) {
								$offset_key						=	":".$key.$offset.$i;
								$columns['bind'][$offset_key]	=	$values[$key];
								$columns['update'][$offset_key]	=	"`".$key."` = ".$offset_key;
								$columns['insert'][$offset_key]	=	$offset_key;
								$columns['columns'][$key]		=	"`".$key."`";
							}
						}
					}
					else {
						if(isset($values[$key]) && !empty($values[$key])) {
							$offset_key						=	":".$key.$offset.$i;
							$columns['bind'][$offset_key]	=	$values[$key];
							$columns['update'][$offset_key]	=	"`".$key."` = ".$offset_key;
							$columns['insert'][$offset_key]	=	$offset_key;
							$columns['columns'][$key]		=	"`".$key."`";
						}
					}
				}
				
				if(isset($columns)) {				
					$columns['sql']['columns']			=	implode(",",$columns['columns']);
					$columns['sql']['insert']['vals']	=	"(".implode(",",$columns['insert']).")";
					$columns['sql']['update']['vals']	=	implode(",",$columns['update']);
					$columns['sql']['bind']				=	$columns['bind'];
				}
				$i++;
			}
					
			if(isset($columns))
				return $columns;
		}