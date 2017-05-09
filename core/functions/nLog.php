<?php
function nLog($e, $path = false)
	{
		$path		=	(empty($path))? __DIR__.DS.'..'.DS.'..'.DS.'client'.DS.'settings'.DS.'errorlogs'.DS.'app_error.txt' : $path;
		$dirPath	=	pathinfo($path,PATHINFO_DIRNAME);
		if(!is_dir($dirPath))
			mkdir($dirPath,0755,true);
		if(!is_file($dirPath.'/.htaccess'))
			file_put_contents($dirPath.'/.htaccess','Order Allow,Deny'.PHP_EOL.'Allow from all');
		$log	=	strip_tags($e->getMessage().printpre($e->getTrace(),array('backtrace'=>false)));
		file_put_contents($path,$log);
	}