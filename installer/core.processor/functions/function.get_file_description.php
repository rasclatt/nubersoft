<?php
/*Title: get_file_description()*/
/*Description: Gets the description of the document (a.k.a. this)*/
/*Settings: `$filename {str}` - Requires valid file.*/

	function get_file_description($filename = false,$lines = 10)
		{
			register_use(__FUNCTION__);
			if(!is_file($filename))
				return;
			
			AutoloadFunction('read_from_file,check_empty');
			// Fetch the first 10 lines of the file.
			$file	=	read_from_file($filename,$lines);
			// Search for notes
			preg_match_all('!/\*([^\*\:]{1,})\:([^\*]{1,})\*/!',$file,$matches);
			
			if(!empty($matches[2])) {
				$dup	=	false;
				$mCount	=	count($matches[1]);
				for($i = 0; $i < $mCount; $i++) {
					$key	=	str_replace(" ","_",strtolower($matches[1][$i]));
					$val	=	$matches[2][$i];
					
					if(!isset($array[$key]))
						$array[$key]	=	$val;
					else {
						$dup	=	true;
						$a		=	(!isset($a))? 1:$a+1;
						$array[$key."_$a"]	=	$val;
					}
				}
					
				if(isset($array)) {
					if($dup == true && (!isset($array[$key."_0"]) && isset($array[$key]))) {
						$array[$key."_0"]	=	$array[$key];
						unset($array[$key]);
					}
				}
					
				$matches	=	(isset($array))? $array:$matches;
				ksort($matches,SORT_REGULAR);
			}
			
			if(isset($matches[0])) {
				if(empty($matches[0]))
					$matches	=	false;
			}

			return (!empty($matches))? $matches:false;
		}