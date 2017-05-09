<?php
/*Title: combine_arrays()*/
/*Description: This function just combines arrays in the `DBWriter()` class. Generally, it will combine a file, request, and/or filtered `array` to allow for valid `column` names for `database` insertion. Outside of this scope, is not really valuable.*/

	function combine_arrays($arr1 = array(),$arr2 = array(), $allow_empty = true)
		{
			
			if(empty($arr1))
				return;
				
			foreach($arr1 as $key => $value) {
					if(empty($value))
						$arr1[$key]	=	(isset($arr2[$key]))? $arr2[$key]: $value;
					else {
							if(is_array($value))
								$arr1[$key]	=	json_encode($value);
						}
				}
				
			foreach($arr2 as $key => $value) {
					if(!isset($arr1[$key]))
						$arr1[$key]	=	(is_array($value))? json_encode($value) : $value;
				}

			return ($allow_empty)? $arr1 : array_filter($arr1);
		}
?>