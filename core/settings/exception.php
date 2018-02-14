<?php
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
		die(nApp::getErrorLayout('noreg'));
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
			# If there is a caught Database exception
			if($e instanceof \PDOException) {
				# Set error
				$nApp->setSession('app_error','Database error occurred.');
				# Save the error to log file
				$nApp->autoload('nLog');
				\nLog($e);
				# Redirect back
				header('Location: /?error=sql');
				exit;
			}
			else {
				$firstrun	=	(!empty($_SESSION['first_run']));
				$msg		=	($is_admin || $firstrun)? $e->getMessage() : 'View log for details.';
				echo '<p style="font-family: helvetica, sans-serif;">Unrecoverable Application Error. '.$msg.'</p>';
			}
		}
}