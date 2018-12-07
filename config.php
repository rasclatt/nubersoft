<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
# Includes application defaults
require(__DIR__.DIRECTORY_SEPARATOR.'defines.php');
# Include client defines
if(is_file($client_defines = NBR_CLIENT_DIR.DS.'defines.php')) {
	include_once($client_defines);
}
# Include composer if set
if(is_file($vendor = NBR_ROOT_DIR.DS.'vendor'.DS.'autoload.php')) {
	include_once($vendor);
}
# Create localized autoloader
spl_autoload_register(function($class){
	# Find in vendor
	$class	=	str_replace(DS.DS, DS, NBR_ROOT_DIR.DS.'vendor'.DS.str_replace('\\', DS, $class).'.php');
	# Include if found
	if(is_file($class)) {
		include_once($class);
	}
});