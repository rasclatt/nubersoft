<?php
namespace Nubersoft;

class nFtp extends \Nubersoft\nFunctions
	{
		private	$con,
				$current,
				$errors,
				$root,
				$listed;
		
		public	function __construct($host,$user,$pass,$root = false,$port = 21,$timeout = 90)
			{
				$this->root	=	$root;
				$this->con	=	ftp_connect($host,$port,$timeout);
				if (!$this->con) {
					trigger_error('Connection to FTP failed.',E_USER_NOTICE);
					//throw new \Exception('Connection to FTP failed.');
					return $this;
				}
				
				$login		=	ftp_login($this->con, $user, $pass); 
				
				if(!$login) {
					trigger_error('Login failed.',E_USER_NOTICE);
					//throw new \Exception('Login failed.');
					return $this;
				}
				
				if($this->root)
					$this->changeDir($this->root);
				
				return $this;
			}
		
		public	function setRoot($root)
			{
				$this->root	=	$root;
				return $this;
			}
		
		public	function stripRootAppend($path,$append=false)
			{
				return	str_replace('/',DS,str_replace('//','/',$append.'/'.str_replace($this->root,'',$path)));	
			}
		
		public	function changeDir($path)
			{
				ftp_chdir($this->con,$path);
				return $this;
			}
		
		public	function currentDir()
			{
				return ftp_pwd($this->con);
			}
		
		public	function close()
			{
				ftp_close($this->con);
			}
			
		public	function dirList($path=false)
			{
				return (!empty($path))? ftp_nlist($this->con,$path) : ftp_nlist($this->con,$this->currentDir());
			}
			
		public	function goTo($path)
			{
				$this->changeDir(str_replace('//','/',$this->currentDir().'/'.$path));
				return $this;
			}
		
		public	function doWhile($from,$to,$func)
			{
				# Open file
				$file		=	fopen($to,'w');
				$content	=	ftp_nb_fget($this->con, $file, $from, FTP_BINARY);
				while($content == FTP_MOREDATA) {
					$func($from,$to);
					# Continue downloading...
					$content	=	ftp_nb_continue($this->con);
				}
				
				if($content != FTP_FINISHED) {
				   $this->errors[]	=	$from;
				}
				
				fclose($file);
			}
			
		public	function recurseDownload($directories,$root,$ext=array('jpg','jpeg','gif','png'))
			{
				if(count($this->listed) >= 50) {
					return $this;
				}
				
				foreach($directories as $path) {
					if(ftp_size($this->con,$path) == '-1') {
						$this->recurseDownload($this->changeDir($path)->dirList(),$root,$ext);
					}
					else {
						if(in_array(strtolower(pathinfo($path,PATHINFO_EXTENSION)),$ext)) {
							$this->listed['from'][]	=	$path;
							$this->listed['to'][]	=	$this->stripRootAppend($path,$root);
						}
					}
				}
				
				return $this;
			}
		
		public	function getList($type=false)
			{
				if($type)
					return (isset($this->listed[$type]))? $this->listed[$type] : false;
					
				return $this->listed;
			}
	}