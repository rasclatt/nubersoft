<?php
include_once(__DIR__.'/../config.php');
if(!is_admin())
	return;

if(is_file($ht = __DIR__.'/../.htaccess')) {
		$htacces	=	file_get_contents($ht);
		echo PHP_EOL.$htacces;
		exit;
	}
else {
		autoload_function("get_default_htaccess");
		$htacces	=	get_default_htaccess();
		echo '#FETCHED FROM DEFAULT -> YOU HAVE NO .htaccess IN PLACE!'.PHP_EOL.$htacces;
		exit;
		
	}