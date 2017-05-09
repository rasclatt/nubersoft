<?php
namespace	Nubersoft;

class	Initializer extends \Nubersoft\nFunctions
	{
		public	static	function getSalt()
			{
				$dir	=	NBR_CLIENT_DIR.DS.'settings'.DS.'encryptions'.DS;
				if(!$this->isDir($dir,true,0755))
					return false;
				
				if(!$this->isProtected('server_rw'))
					return false;
				
				
			}
	}