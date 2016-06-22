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
					
					if(is_file(CLIENT_DIR.'/settings/api.php'))
						include(CLIENT_DIR.'/settings/api.php');
				} 
		}
?>