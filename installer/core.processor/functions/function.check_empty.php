<?php
/*Title: check_empty()*/
/*Description: This function just matches an array key to see if the value is set and not empty.*/
/*Example: 
`$_POST['command'] = 1;
if(check_empty('command',$_POST)) { // Do stuff because $_POST['command'] is set and has a value }`
*/
/*Example 2 (match value):
`if(check_empty('command',$_POST,1)) { //Do stuff because $_POST['command'] exists and the value equals 1 }`*/

	function check_empty($array = false,$key = false,$value = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('validate_var');
			if(validate_var($array,$key)) {
					if(!empty($array[$key])) {
							// If there is a value component return true or false if it matches value
							if($value != false)
								return ($array[$key] === $value);
							// If the value is not empty
							return true;
						}
				}
			// If gets to here, empty
			return false;
		}
?>