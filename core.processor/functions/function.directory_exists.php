<?php
function directory_exists($_directory = '',$options = array())
	{
		
		$_permissions	=	(isset($options['perm']) && is_numeric($options['perm']))? str_pad($options['perm'],4,0,STR_PAD_LEFT) : 0755;
		$makedir 		=	(!empty($options['make']));
		
		// If the directory is not empty
		if(!empty($_directory)) {
			// If the directory does not exist
			if(!is_dir($_directory)) {
				// If check is false, try and create directory
				if($makedir)
					return mkdir($_directory,$_permissions,true);
			}
			else
				return true;
		}
			
		// Return false that directory exists
		return false;
	}