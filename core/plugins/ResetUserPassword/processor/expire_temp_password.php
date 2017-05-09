<?php
// Save default timezone
// Assign timezone
\Nubersoft\nApp::call()->autoload(array('get_timezone'));
date_default_timezone_set(get_timezone());
// Assign times
$now		=	strtotime("now");
$expire		=	strtotime($result[0]['timestamp']." + 1 hour");
// Check if the expiration is set
$expired	=	($expire < $now);
// Save result to register
// If the expired
if($expired) {
	$nubquery	->update("users")
				->set(array("timestamp"=> '', "reset_password"=>''))
				->where(array("username"=>self::$username))
				->write();
				
	\Nubersoft\nApp::call()->saveSetting('password_reset',array("reset"=>false,"expired"=>true));
}
else {
	// If the validation is good, allow in and reset 
	if($result[0]['reset_password'] == self::$password) {
		$nubquery	->update("users")
					->set(array("timestamp" => '', "reset_password" => ''))
					->where(array("username" => self::$username))
					->write();

		// Validate true
		$validate = true;
		\Nubersoft\nApp::call()->saveSetting('password_reset',array("reset"=>true,"expired"=>false));
	}
}