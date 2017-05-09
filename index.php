<?php
try {
	# Configuration
	require(__DIR__.DIRECTORY_SEPARATOR.'config.php');
	# Try and run the default nubersoft campaign
	\Nubersoft\NuberEngine::init()->core($nAutomator);
}
catch (Exception $e) {
	$is_admin	=	(isset($_SESSION['usergroup']) && $_SESSION['usergroup'] <= 2);
	
	if(!defined('DS'))
		define('DS',DIRECTORY_SEPARATOR);
	# Get the code for the base problem
	$code	=	$e->getCode();
	# Create some defines
	if(!defined('NBR_CLIENT_DIR'))
		define('NBR_CLIENT_DIR',__DIR__.DS.'client_assets');
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
			$msg	=	($is_admin)? $e->getMessage() : 'An error occurred.';
			echo '<p style="font-family: helvetica, sans-serif;">'.$msg.'</p>';
	}
}