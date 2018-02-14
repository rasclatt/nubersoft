<?php
/**
*	@Copyright	nUberSoft.com All Rights Reserved.
*	@License	License available for review in license text document in root
*/
namespace Nubersoft;
# Shortcut Flag Controller
use \Nubersoft\Flags\Controller as Flag;
use \Nubersoft\nLocale as Locale;
# Turn off errors
$nApp->setErrorMode(Flag::hasFlag('devmode'));
# Store the main functions object
$nApp->saveEngine('\Nubersoft\nFunctions', new nHtml(), new nImage());
# Load the backtracer
$nApp->getHelper('nFunctions')->autoload('printpre');
# Create block instructions for header-based commands
$order		=	$nApp->jsonFromFile($nApp->getSystemFile('register'.DS.'automation.json'));
# Get the automator
$nAutomator	=	new nAutomator((new Locale())->getTimeZone(),true);
# Run through all the flows
$nAutomator->listen($order,'action');
# Try and run the default application
NuberEngine::init()->core($nAutomator);