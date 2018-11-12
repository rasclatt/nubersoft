<?php
namespace Nubersoft;
# Add our application config
require(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'config.php');
# Create instance of the main class
$Application	=	nApp::call();

try {
	# Start buffering
	ob_start();
	# Create a container application
	$Application->createContainer(function(
		nApp $nApp,
		nSession $Session,
		nGlobal\Observer $nGlobal,
		nAutomator\Controller $AutomatorController
	){
		# Load our hand print_r substitute
		$nApp->autoload('printpre');
		# Start the session
		$Session->start();
		# Convert all request forms to data node(s)
		$nGlobal->listen();
		# Start our program
		$AutomatorController->createWorkflow('installer');
	});
	# Get the normal buffer
	$data	=	ob_get_contents();
	# Stop the normal buffer
	ob_end_clean();
	# Write the normal buffer
	echo $data;
}
catch(HttpException $e) {
	# Stop the normal buffer (don't output)
	ob_end_clean();
	# Start our automator
	$Automator	=	$Application->getHelper('nAutomator\Observer');
	# Get our data obj
	$Node		=	$Application->getHelper('DataNode');
	# Save the message to data node
	$Node->addNode('_MESSAGES',[
		'msg'=>$e->getMessage(),
		'code'=>$e->getCode()
	]);
	# Set the default layout workflow to create
	switch($e->getCode()) {
		case(101):
			$layout	=	'offline';
			break;
		case(102):
			$layout	=	'maintenance';
			break;
		case(103):
			$layout	=	'installer';
			break;
		default:
			$layout	=	'error';
	}
	# Start our program
	$Automator
		->setWorkflow($layout)
		# Listen for the "action" key
		->setActionKey((defined('NBR_ACTION_KEY')? NBR_ACTION_KEY : 'action'))
		# Run the automator
		->listen();
}