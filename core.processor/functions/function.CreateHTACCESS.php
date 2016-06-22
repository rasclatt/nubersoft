<?php
function CreateHTACCESS($settings = false)
	{
		$script	=	(!empty($settings['script']))? $settings['script']: false;
		$dir	=	(!empty($settings['dir']))? $settings['dir']: false;
		$rule	=	(!empty($settings['rule']))? $settings['rule']: 'server_rw';
		$mkdir	=	(!empty($settings['make']))? $settings['make']: false;
		
		if(!$dir)
			return false;
			
		AutoloadFunction("get_default_htaccess");
			
		$writer['server_rw']	=	"Order Deny,Allow
Deny from all
Allow from 127.0.0.1

<Files /index.php>
    Order Allow,Deny
    Allow from all
</Files>";
		// Save the default acction
		$writer['default']		=	get_default_htaccess();
		$writer['server_r']		=	"Order Allow,Deny
Allow from all";
			
		if(!isset($writer[$rule]) && !$script)
			return false;
		
		$usescript	=	($script != false)? $script : $writer[$rule];
		
		if($mkdir && !is_dir($dir))
			mkdir($dir,0755,true);
		
		if(!is_dir($dir))
			return false;
			
		if(!is_file($writefile = str_replace(_DS_._DS_,_DS_,$dir._DS_.".htaccess"))) {
			get_default_htaccess(array("dir"=>$dir,"write"=>true,"htaccess"=>$usescript));
		}
	}