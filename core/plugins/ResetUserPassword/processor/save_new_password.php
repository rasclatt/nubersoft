<?php
$pEngine	=	\PasswordGenerator::Engine(\PasswordGenerator::USE_DEFAULT);
$currPass	=	\Nubersoft\nApp::call()->getPost('password');
$password	=	$pEngine->encrypt_password($currPass)->get_hash();
// Update
$nubquery	->update("users")
			->set(array("password"=>$password,"timestamp"=>NULL,"reset_password"=>NULL))
			->where(array("ID"=>\Nubersoft\nApp::call()->getPost('ID')))
			->write();
// Pass an error message
\Nubersoft\nApp::call()->saveIncidental('password_reset',array('success'=>true,'msg'=>"Password reset successfully."));