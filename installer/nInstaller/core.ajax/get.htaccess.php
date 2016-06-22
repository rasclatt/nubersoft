<?php
include_once(__DIR__.'/../dbconnect.root.php');
if(!is_admin())
	return;

if(is_file($ht = __DIR__.'/../.htaccess')) {
		$htacces	=	file_get_contents($ht);
		echo PHP_EOL.$htacces;
		exit;
	}
else {
		AutoloadFunction("get_default_htaccess");
		$htacces	=	get_default_htaccess();
		echo '#FETCHED FROM DEFAULT -> YOU HAVE NO .htaccess IN PLACE!'.PHP_EOL.$htacces;
		exit;
		
	}