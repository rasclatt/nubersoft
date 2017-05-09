<?php	
/*Title: error_check()*/
/*Description: This just checks if errors are set to report `PHP` errors using `ini_set("display_errors",1);` and `error_reporting(E_ALL);`.*/

	function error_check()
		{
			
			return (defined('SERVER_MODE') && SERVER_MODE == true)? true:false;
		}
?>