<?php
/*Title:  get_default_htaccess()*/
/*Description: This function will write to disk the default `.htaccess` or any .htaccess script fed into it as an arguement*/

function get_default_htaccess($settings = false)
	{
		register_use(__FUNCTION__);
		if(!empty($settings['htaccess']))
			$data	=	$settings['htaccess'];
		else
			$data	=	'RewriteEngine On
## FORCE HTTPS -> Uncommment to force ssl
##RewriteCond %{SERVER_PORT} 80 
##RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
## Normal Rewrites
RewriteCond %{REQUEST_URI} !(/$|\.)
RewriteRule (.*) %{REQUEST_URI}/ [R=301,L]
RewriteCond $1 !^(index\.php|images|robots\.txt)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php?$1 [NC,QSA,L]';
		
		if(check_empty($settings,'write',true)) {
			$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_ROOT_DIR;
			return SaveToDisk::Write(array('dir'=>$dir,"filename"=>'.htaccess',"payload"=>$data,"write"=>'w'));
		}

		return $data;
	}