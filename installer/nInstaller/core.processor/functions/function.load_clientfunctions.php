<?php
/*Title: load_clientfunctions()*/
/*Description: This function autoload common default folders. It is used in the config file.*/
	function load_clientfunctions()
		{
			register_use(__FUNCTION__);
			
			AutoloadFunction('get_clientfunctions');
			$autoload[]	=	CLIENT_DIR."/settings/on.loadpage/";
			$autoload[]	=	CLIENT_DIR."/settings/on.login/";
			$autoload[]	=	CLIENT_DIR."/settings/on.logout/";
			$autoload[]	=	CLIENT_DIR."/settings/functions/";
			$autoload[]	=	CLIENT_DIR."/settings/apps/";
			get_clientfunctions($autoload);
		}
?>