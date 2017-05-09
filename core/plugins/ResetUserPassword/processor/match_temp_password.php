<?php
$email		=	\Nubersoft\nApp::call()->getPost('email');
$pass		=	\Nubersoft\nApp::call('Safe')->decode(\Nubersoft\nApp::call()->getPost('password'));
$pEngine	=	\PasswordGenerator::Engine(\PasswordGenerator::USE_DEFAULT);
// Search for submitted email address
$get_new	=	nquery()	->select()
							->from("users")
							->where(array("email"=>$email))
							->getResults();
// If found, continue
if(!empty($get_new[0]['reset_password'])) {
	$verified		=	$pEngine->verify_password($pass,$get_new[0]['reset_password'])->valid;
	// If passwords match, assign data
	if($verified) {
		$this->reset	=	true;
		$this->data		=	$get_new[0];
		\Nubersoft\nApp::call()->saveIncidental('pass_match',array("success"=>true,'msg'=>"Password match."));
		\Nubersoft\nApp::call()->saveSetting("reset_user",$email);
	}
	else {
		\Nubersoft\nApp::call()->saveIncidental('pass_match',array("success"=>false,"msg"=>"Temporary password mis-match."));
	}
}
else {
	\Nubersoft\nApp::call()->saveIncidental('pass_match',array("success"=>false,"msg"=>"error"));
}