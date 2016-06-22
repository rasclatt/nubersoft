<?php
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
				
				if(is_file(NBR_CLIENT_DIR._DS_.'settings'._DS_.'api.php'))
					include(NBR_CLIENT_DIR._DS_.'settings'._DS_.'api.php');
			}
	}