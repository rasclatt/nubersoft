<?php
	function TokenMatch($settings = false)
		{
			$name	=	(!empty($settings['token_name']))? $settings['token_name'] : 'email';
			$req	=	(!empty($settings['request']))? $settings['request'] : 'post';
			
			switch(strtolower($req)) {
				case('request'):
					$arr	=	$_REQUEST;
					break;
				case('get'):
					$arr	=	$_GET;
					break;
				default:
					$arr	=	$_POST;
			}
			
			return nApp::tokenMatch($name, $arr);
		}