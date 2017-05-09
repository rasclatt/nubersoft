<?php
/*
**	@desc	Retrieves the base URL
**	@param	[bool] Applying true will return HTTPS
**/
	function plugins_url($plugins = '/client_assets/apps', $forceSSL = false)
		{
			AutoloadFunction("check_ssl");
			$baseUrl	=	(defined("BASE_URL"))? BASE_URL : 'http://'.$_SERVER['HTTP_HOST'];
			$sslUrl		=	(defined("BASE_URL_SSL"))? BASE_URL_SSL : 'https://'.$_SERVER['HTTP_HOST'];
			$forceSSL	=	(defined("FORCE_URL_SSL"))? FORCE_URL_SSL : $forceSSL;
			
			if($forceSSL)
				return $sslUrl.$plugins;
			else
				return (check_ssl())? $sslUrl.$plugins : $baseUrl.$plugins;
		}