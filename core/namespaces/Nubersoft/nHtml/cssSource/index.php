<?php
$localUrl	=	(!empty($useData['site_url']))? $useData['site_url'] : '';
$path		=	(!empty($useData['path']))? $useData['path'] : false;
$js			=	(!empty($useData['links']))? $useData['links'] : false;
$longPath	=	(!empty($useData['longPath']))? $useData['longPath'] : false;

if(empty($js) || empty($path) || empty($longPath))
	return false;

$url	=	$localUrl.$path;
$path	=	$longPath;

include(realpath(__DIR__.DS.'..').DS.'html'.DS.'stylesheet.php');