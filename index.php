<?php
#check if cron job set
if(!empty($argv[1])) {
	$_REQUEST	=	[];
	parse_str($argv[1],$_REQUEST);
}
# Configuration
require(__DIR__.DIRECTORY_SEPARATOR.'config.php');
# Check if maintenance flag is set
if(\Nubersoft\Flags\Controller::hasFlag('maintenance')) {
	# Render the offline page
	include(__DIR__.DS.'core'.DS.'template'.DS.'default'.DS.'frontend'.DS.'static.offline.php');
	# Stop
	exit;
}
# Try to run the application as usual
try {
	# Create instance for sharing
	$nApp	=	new \Nubersoft\nApp();
	$nApp->setErrorMode();
	# Load our backtracer and load printpre
	$nApp->saveEngine('\Nubersoft\nFunctions', $nApp->getHelper('nHtml'), $nApp->getHelper('nImage'))
		->getHelper('nFunctions')->autoload(array('printpre'));
	# Create block instructions for header-based commands
	$order	=	array(
		'blockflow/session',
		'blockflow/database',
		'blockflow/preferences',
		'blockflow/timezone'
	);
	# Get the automator
	$nAutomator	=	$nApp->getHelper('nAutomator', $nApp);
	# Run through all the flows
	foreach($order as $flow) {
		# Add session observer
		$nAutomator->setListenerName('action')->getInstructions($flow);
	}
	# Try and run the default application
	\Nubersoft\NuberEngine::init()->core($nAutomator);
	
	/*
	if(\Nubersoft\nApp::call()->isAdmin())
		echo printpre(\Nubersoft\nApp::call()->getDataNode('workflow_run'));
	*/
}
catch (\Nubersoft\nException $e) {
	$nApp	=	\Nubersoft\nApp::call();
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
catch (\Exception $e) {
	if(!isset($_SESSION))
		session_start();
	
	$is_admin	=	(isset($_SESSION['usergroup']) && $_SESSION['usergroup'] <= 2);
	
	if(!defined('DS'))
		define('DS',DIRECTORY_SEPARATOR);
	# Get the code for the base problem
	$code	=	$e->getCode();
	# Create some defines
	if(!defined('NBR_CLIENT_DIR'))
		define('NBR_CLIENT_DIR',__DIR__.DS.'client');
	# Set different options for dealing with install/start-up issues
	switch($code) {
		# Missing registry file
		case(404001):
			$pathtoxml	=	'settings'.DS.'registry.xml';
			# Fetch the remote default version
			$getRemote	=	file_get_contents(__DIR__.DS.'core'.DS.$pathtoxml);
			# Save it to default location
			file_put_contents(NBR_CLIENT_DIR.DS.$pathtoxml,$getRemote);
			# Inform user
			die(\Nubersoft\nApp::getErrorLayout('noreg'));
		default:
			$reg = NBR_CLIENT_SETTINGS.DS.'registry.xml';
			if(!is_file($reg)) {
				if(!is_dir($regdir = NBR_CLIENT_SETTINGS))
					mkdir($regdir,0755,true);
				
				copy(NBR_SETTINGS.DS.'registry.xml',$reg);
				echo (is_file($reg))? 'Registry created!' : 'Registry could not be created.';
				if(is_file($reg)) {
					$_SESSION['first_run']	=	$_SERVER['HTTP_HOST'];
					$xml	=	simplexml_load_file($reg);
					$xml->ondefine->base_url	=	
					$xml->ondefine->base_url	=	'http://'.$_SERVER['HTTP_HOST'];
					$xml->asXml($reg);
				}
			}
			else {
				$msg	=	($is_admin || $_SESSION['first_run'])? $e->getMessage() : 'An error occurred.';
				echo '<p style="font-family: helvetica, sans-serif;">'.$msg.'</p>';
				echo \Nubersoft\nApp::call()->safe()->encodeSingle(file_get_contents($reg));
			}
	}
}