<?php
/**
*	@Copyright	nUberSoft.com All Rights Reserved.
*	@License	License available for review in license text document in root
*/
namespace Nubersoft;
# Shortform flags class
use \Nubersoft\Flags\Controller as Flags;
# Include defines
require_once(__DIR__.DIRECTORY_SEPARATOR.'defines.php');
# Add the base class
require_once(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'Singleton.php');
# Add the base function class
require_once(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nFunctions.php');
# Look for custom autoloader
if(is_file($autoload = NBR_CLIENT_SETTINGS.DS.'autoload.php'))
	include_once($autoload);
else {
	# Add the function autoloader
	require_once(NBR_FUNCTIONS.DS.'nloader.php');
	# Create class autoloader
	spl_autoload_register('nloader');
}
# Create instance of nApp AND run session items
$nApp	=	nApp::createContainer(function(nApp $nApp, Submits $Submits){
	# Check for error flag and turn errors on if found
	$nApp->setErrorMode((Flags::hasFlag('errors') || Flags::hasFlag('devmode')));
	# Create session from the start
	$nApp->getHelper('nSessioner','s')->observer();
	# Commit page to data
	$nApp->getPageURI();
	# Sanitize data
	$Submits
		# Save all REQUEST/GET/POST
		->sanitize()
		# Save SESSION
		->setSessionGlobal()
		# SAVE SERVER / USER AGENT
		->sanitizeServer();
	# Send back the main app
	return $nApp;
});
