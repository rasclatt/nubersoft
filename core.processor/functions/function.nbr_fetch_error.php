<?php
function nbr_fetch_error($code = false,$file,$line)
	{
		$error['whitelist']	=	array(
			"content"=>"IDENTITY REFUSED: (".$_SERVER['REMOTE_ADDR'].") Whitelist".PHP_EOL."{$file} | {$line}".PHP_EOL,
			'die'=>'<h1>Error: 550</h1></p>Permission denied using '.$_SERVER['REMOTE_ADDR'].'</p>',
			'headers'=>array('http/1.1 550 permission denied')
		);
		
		$error['login']	=	array(
			"content"=>"LOGIN: ".\nApp::getPost('username').PHP_EOL."SUCCESS: ".\nApp::getDataNode('user')->loggedin." ADMIN: ".is_admin()."/".\nApp::getDataNode('user')->admission.PHP_EOL."FILE/LINE: {$file}/{$line}",
			"die"=>false,
			"headers"=>false);
		
		$error['unknown']	=	array(
			"content"=>"UNKNOWN ERROR: ".$_SERVER['REMOTE_ADDR'].PHP_EOL." | {$file} | {$line}".PHP_EOL,
			'die'=>false,
			'headers'=>false
		);
		
		
		return (isset($error[$code]))? $error[$code] : $error['unknown'];
	}