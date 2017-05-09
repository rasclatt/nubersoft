<?php
$rEngine	=	\Nubersoft\nApp::call()->toArray(\Nubersoft\nApp::call()->getEngine('reset'));
if(!empty($rEngine['reset'])) {
	$reset		=	(isset($rEngine->reset))? $rEngine->reset : false;
	$data		=	(isset($rEngine->data))? $rEngine->data : false;
	include(__DIR__.DS.'..'.DS.'renderlib'.DS.'form_change_password.php');
	exit;
}