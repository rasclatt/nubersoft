<?php
// See if the db connection is valid
$siteValid	=	(empty(\Nubersoft\nApp::call()->getDataNode('connection')->health));
// If user is logged in or the site is not working
if(\Nubersoft\nApp::call()->getFunction('is_loggedin') || $siteValid) {
	// If is ajax request
	if(\Nubersoft\nApp::call()->isAjaxRequest())
		exit;
	else
		return;
}
// Path to submit form
$path	=	array(
				__DIR__,
				'..',
				'renderlib',
				'message_reset.php'
			);
// Render the submit form
echo \Nubersoft\nApp::call()->render($path);
// If the request was an ajax request, stop
if(\Nubersoft\nApp::call()->isAjaxRequest())
	exit;