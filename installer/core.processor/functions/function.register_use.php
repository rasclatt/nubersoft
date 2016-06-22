<?php
	function register_use($register = false)
		{
			if(empty($register) || $register == false)
				return;
				
			$err_reporting	=	(defined('SERVER_MODE') && SERVER_MODE == true)? true:false;
			// Turn on global reporting		
			if($err_reporting) {
					global $_cLoaded;
					$_cLoaded['used'][]	=	$register;
				}
		}
?>