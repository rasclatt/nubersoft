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
# Check for error flag and turn errors on if found
if(\Nubersoft\Flags\Controller::hasFlag('errors')) {
	error_reporting(E_ALL);
	ini_set('display_errors',1);
}