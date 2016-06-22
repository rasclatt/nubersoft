<?php
/*Title: load_clientfunctions()*/
/*Description: This function autoload common default folders. It is used in the config file.*/
	function load_clientfunctions()
		{
			
			
			AutoloadFunction('get_clientfunctions');
			$autoload[]	=	NBR_CLIENT_DIR."/settings/on.loadpage/";
			$autoload[]	=	NBR_CLIENT_DIR."/settings/on.login/";
			$autoload[]	=	NBR_CLIENT_DIR."/settings/on.logout/";
			$autoload[]	=	NBR_CLIENT_DIR."/settings/functions/";
			$autoload[]	=	NBR_CLIENT_DIR."/settings/apps/";
			get_clientfunctions($autoload);
		}
?>