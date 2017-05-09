<?php
	class FetchCreds
		{
			public	$_creds;
			
			private	$folder;
			
			public	function __construct()
				{
					$this->getCreds();
				}
				
			public	function setCreds($folder)
				{
					$this->folder	=	$folder;
					return $this;
				}
			
			public	function getCreds()
				{
					$base			=	true;
					$this->_creds	=	array();
					$this->folder	=	(!empty($this->folder))? $this->folder : NBR_CLIENT_DIR.'/settings/dbcreds.php';
					
					if(is_file($this->folder))
						include($this->folder);
					
					return $this;
				}
			
			public	function returnCreds()
				{
					return $this->_creds;
				}
		}