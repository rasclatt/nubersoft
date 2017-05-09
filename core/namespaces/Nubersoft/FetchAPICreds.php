<?php
namespace Nubersoft;

class FetchAPICreds
	{
		public	$_creds;
		public	function __construct()
			{
				$this->API();
			}
			
		public	function API()
			{
				$base	=	true;
				
				if(is_file(NBR_CLIENT_SETTTINGS.DS.'api.php'))
					include(NBR_CLIENT_SETTINGS.DS.'api.php');
			}
	}