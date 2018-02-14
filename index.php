<?php
/**
*	@Copyright	nUberSoft.com All Rights Reserved.
*	@License	License available for review in license text document in root
*/
namespace Nubersoft;
# Shortcut Flag Controller
use \Nubersoft\Flags\Controller as Flag;
#check if cron job set
if(!empty($argv[1])) {
	$_REQUEST	=	[];
	parse_str($argv[1],$_REQUEST);
}
# Configuration (includes defines)
require(__DIR__.DIRECTORY_SEPARATOR.'config.php');
# Check if maintenance flag is set
if(Flag::hasFlag('maintenance') && !$nApp->isAdmin() && !$nApp->isAdminPage() && !$nApp->isAjaxRequest()) {
	# Check if there is a template set for the static page
	$errorTemplate	=	nTemplate::getFileFromDefaultTemplate('frontend'.DS.'static.offline.php');
	# Render the offline page
	die((is_file($errorTemplate))? $nApp->render($errorTemplate) : '<h1>'.$nApp->__('Site is offline for maintenance.').'</h1>');
}
# Try to run the application as usual
try {
	# First check if there is a client application to run
	$index	=	NBR_CLIENT_SETTINGS.DS.'index.php';
	# Include the valid application
	include((is_file($index))? $index : NBR_SETTINGS.DS.'index.php');
}
catch (nException $e) {
	$exception	=	NBR_CLIENT_SETTINGS.DS.'nexception.php';
	include((is_file($exception))? $exception : NBR_SETTINGS.DS.'nexception.php');
}
catch (\Exception $e) {
	$exception	=	NBR_CLIENT_SETTINGS.DS.'exception.php';
	include((is_file($exception))? $exception : NBR_SETTINGS.DS.'exception.php');
}