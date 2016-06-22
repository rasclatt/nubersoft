<?php
/*
** @param - $dir -> string or array
** @param - $make -> bool
** @description - If folder is valid, will create .htaccess that only allows PHP to access the file directory 
*/
	function protect_directory($dir = false)
		{
			// If directory is invalid return false
			if(empty($dir))
				return false;
			// Load the .htaccess function
			AutoloadFunction("CreateHTACCESS");
			// Check if there are more than one directories set to protect
			if(!is_array($dir) && is_dir($dir)) {
					if(!is_file(str_replace("//","/",$dir."/.htaccess")))
						CreateHTACCESS(array("rule"=>"server_rw","dir"=>$dir));
				}
			elseif(is_array($dir)) {
					foreach($dir as $directory)
						protect_directory($directory);
				}
		}