<?php
/*Title: AutoloadFunction()*/
/*Description: This function will try and load any file located in the the function folder in the `/core.processor/function` folder.*/
function AutoloadFunction($function = false,$loaddir = false)
	{
		if(!$function)
			return false;
		// If not an array, try and explode string
		if(!is_array($function)) {
				if(strpos($function,","))
					$functions	=	explode(",",$function);
			}
		else
			$functions	=	$function;
		// Set the default destination
		$function_dir	=	($loaddir && !is_array($loaddir))? $loaddir.'/function.' : FUNCTIONS.'/function.';
		// Save the function to an array if not already an array
		if(!isset($functions))
			$functions[]	=	$function;
		// Loop through the functions array and load if not already loaded
		foreach($functions as $v) {
			// See if function exists
			if(!function_exists($v)) {
				if(is_file($dir = $function_dir.$v.'.php'))
					include_once($dir);
			}
		}
	}