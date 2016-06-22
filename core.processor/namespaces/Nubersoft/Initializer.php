<?php
namespace	Nubersoft;

class	Initializer
	{
		public	static	function getSalt()
			{
				$dir	=	NBR_CLIENT_DIR._DS_.'settings'._DS_.'encryptions'._DS_;
				if(!\nApp::nFunc()->isDir($dir,true,0755))
					return false;
				
				if(!\nApp::nFunc()->isProtected('server_rw'))
					return false;
				
				
			}
	}