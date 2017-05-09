<?php
/*
**	@Copyright	nUberSoft.com All Rights Reserved.
**	@License	License available for review in license text document in root
*/
# Add important constants
$defines	=	__DIR__.DIRECTORY_SEPARATOR.'defines.php';
if(!is_file($defines))
	throw new Exception('This application requires certain constants to function. Re-install or replace the defines file.');
# Include defines
require_once($defines);
# Add the function autoloader
require_once(NBR_FUNCTIONS.DS.'nloader.php');
# Add the base class
require_once(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'Singleton.php');
# Add the base function class
require_once(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nFunctions.php');
# Create class autoloader
spl_autoload_register('nloader');
# Use nApp as the main loader
use Nubersoft\nApp as nApp;
# Create instance for sharing
$nApp	=	new nApp();
# Load our backtracer and load printpre
$nApp->saveEngine('\Nubersoft\nFunctions', $nApp->getHelper('nHtml'), $nApp->getHelper('nImage'))
	->getHelper('nFunctions')->autoload(array('printpre'));
# Create a general error message
$msg	=	'Whoops! Our fault, an error occurred displaying the page.';
# Try loading some initilizers
try {
	$order	=	array(
		'blockflow/session',
		'blockflow/database',
		'blockflow/preferences',
		'blockflow/timezone'
	);
	# Get the automator
	$nAutomator	=	$nApp->getHelper('nAutomator', $nApp);
	$i = 0;
	# Run through all the flows
	foreach($order as $flow) {
		# Add session observer
		$nAutomator
			->setListenerName('action')
			->getInstructions($flow);
		$i++;
	}
}
catch (Nubersoft\nException $e) {
	if($nApp->isAdmin() || !is_file(__DIR__.DS.'.htaccess')) {
		echo $e->getMessage().printpre($e->getTrace());
		die(printpre(__FILE__.' ('.__LINE__.')'));
	}
	else {
		$nApp->autoload('nLog');
		nLog($e);
		die($msg);
	}
}
catch (Exception $e) {
	if($nApp->isAdmin() || !is_file(__DIR__.DS.'.htaccess')) {
		echo $e->getMessage().printpre($e->getTrace());
		die(printpre(__FILE__.' ('.__LINE__.')'));
	}
	else {
		$nApp->autoload('nLog');
		if(function_exists('nLog')) {
			nLog($e);
		}
		die($msg);
	}
}