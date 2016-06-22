<?php
	function organize($array = array(), $assockey = false,$forcerows = false)
		{
			register_use(__FUNCTION__);
			if(!empty($array)) {
					$i = 0;
					foreach($array as $rows) {
							if(is_array($rows)) {
									foreach($rows as $key => $value) {
											if(!empty($rows[$assockey])) {
												$_key	=	$rows[$assockey];
												$new[$_key][$i][$key]	=	$value;
											}
										}
								}
							$i++;
						}
				}
			if(isset($new)) {
					foreach($new as $key => $value) {
							if(count($value) == 1) {
									$keyName	=	array_keys($value);
									$new[$key]	=	($forcerows == true)? array_values($value):$value[$keyName[0]];
								}
							else {
									$new[$key]	=	array_values($value);
								}
						}
				}
			
			return (isset($new))? $new : $array;
		} ?>