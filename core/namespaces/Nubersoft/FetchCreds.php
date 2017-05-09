<?php
namespace Nubersoft;

class FetchCreds
	{
		private	$_creds,
				$folder;
		
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
				$this->folder	=	(!empty($this->folder))? $this->folder : NBR_CLIENT_SETTINGS.DS.'dbcreds.php';
				
				if(is_file($this->folder))
					include($this->folder);
				
				return $this;
			}
		
		public	function returnCreds($type = false)
			{
				if(!empty($type))
					return (isset($this->_creds[$type]))? base64_decode($this->_creds[$type]) : false;
					
				return $this->_creds;
			}
		
		public	function getFolder($isFile = false)
			{
				if($isFile)
					return (is_file($this->folder));
					
				return $this->folder;
			}
		
		public	function createFile($array)
			{
				$create[]	=	'<?php';
				foreach($array as $type => $creds) {
					$create[]	=	'$this->_creds["'.$type.'"]	=	"'.base64_encode($creds).'";';
				}
				
				$script	=	implode(PHP_EOL,$create);
				$nApp	=	\Nubersoft\nApp::call();
				
				if($nApp->isDir($file = NBR_CLIENT_DIR.DS.'settings'.DS))
					$nApp->saveFile($script,$file.'dbcreds.php');
			}
		
		public	function __call($name,$args = false)
			{
				return $this->returnCreds(strtolower(str_replace('get','',$name)));
			}
	}