<?php
namespace Nubersoft;

class	nRequester extends \Nubersoft\nApp
	{
		public	function isSsl($setting=false)
			{
				$httpsCheck['HTTP']				=	(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')? "s":"";
				$httpsCheck['HTTP_XFWD']		=	(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))? ltrim($_SERVER['HTTP_X_FORWARDED_PROTO'],"http") : "";
				$httpsCheck['HTTP_XFWD_PORT']	=	(!empty($_SERVER['HTTP_X_FORWARDED_PORT']))? (($_SERVER['HTTP_X_FORWARDED_PORT'] == '443')? "s":"") : "";
				$httpsCheck['HTTP_PORT']		=	(!empty($_SERVER['SERVER_PORT']))? (($_SERVER['SERVER_PORT'] == '443')? "s":"") : "";
				$httpsCheck['HTTP_SCHEME']		=	(!empty($_SERVER['REQUEST_SCHEME']))? ltrim($_SERVER['REQUEST_SCHEME'],"http") : "";
				$httpsCheck['HTTP_MINE']		=	(!empty($setting))? 's' : '';
				$httpsCheck						=	array_filter($httpsCheck);
				
				return (!empty($httpsCheck) && in_array("s",$httpsCheck))? "s":"";
			}
	}