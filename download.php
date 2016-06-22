<?php
	include_once('config.php');
	// Trim the file name
	$file		=	(!empty($_REQUEST['file']))? trim($_REQUEST['file']):false;
	$dlTemplate	=	(defined("DL_TEMPLATE"))? DL_TEMPLATE : false;
	// Create new downloader
	DownloadEngine::init()->Initialize($file)->Download();
							
	// If download fails, echo the download page
	echo DownloadEngine::init()->ErrorPage($dlTemplate);