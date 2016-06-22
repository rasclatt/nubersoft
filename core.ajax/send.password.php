<?php
	include_once(__DIR__.'/../config.php');
	AutoloadFunction("is_loggedin");
	
	
	if(is_loggedin() || empty(NubeData::$settings->connection->health))
		exit;
	
	AutoloadFunction('check_empty,nQuery');
	
	$REQUEST	=	(isset(NubeData::$settings->_POST->data))? Safe::to_array(NubeData::$settings->_POST->data) : false;
	if(!$REQUEST)
		$REQUEST	=	(isset(NubeData::$settings->_POST))? Safe::to_array(NubeData::$settings->_POST) : false;
	
	// Assign timezone
	date_default_timezone_set(NubeData::$settings->timezone);
	// If the reset button is set, it's go time
	if(check_empty($REQUEST,'command','forgot_pass')) {
			$nubquery	=	nQuery();
			if(filter_var($REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
					$fetch	=	$nubquery	->select(array('email'))
											->from("users")
											->where(array('email'=>$REQUEST['email']))
											->fetch();
											
					$email	=	(isset($fetch[0]['email']))? Safe::decode($fetch[0]['email']) : false;
					if($email !== false && filter_var($email,FILTER_VALIDATE_EMAIL)) {
							try {
									$temp	=	$nubquery	->select(array('reset_password','timestamp'))
															->from("users")
															->where(array("email"=>$email))
															->fetch();
								}
							catch (ResetException $e) {
									echo printpre($e);
								}
							
							if(isset($temp[0]['reset_password']) && !empty($temp[0]['reset_password'])) {
									$pass		=	$temp[0]['reset_password'];
									$stamp		=	$temp[0]['timestamp'];
									$expire		=	strtotime($stamp."+ 1 hour");
									$now		=	strtotime("now");
									$run		=	($now < $expire)? false : true;
								}
							else
								$run	=	true;
							
							if($run) {
									
									AutoloadFunction("create_random_password");
									$pass		=	create_random_password();
									$hash		=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT)->encrypt_password($pass)->get_hash();
									$stamp		=	date("Y-m-d H:i:s",strtotime("now"));
									$nubquery	->update("users")
												->set(array('reset_password'=>$hash,"timestamp"=>$stamp))
												->where(array("email"=>$email))
												->write();
								}
							
							$msg			=	($run)? "Please login in with this password: ".$pass:"You have already sent a password request change. Please wait at least an hour from your first request.";
							
							$returned['h']	=	($run)? "Email Sent Successfully.":"Hmm. This seems familiar somehow.";
							$returned['p']	=	($run)? "Your temporary password has been sent to you.":"Please wait an hour before resubmitting your email request.";
							// Send email
							$Mailer			=	new EmailEngine();
							$sent			=	$Mailer->SetPrefs("Password Reset", $Mailer->Message(array('h1'=>'Password Reset','message'=>"This is an automated message for resetting your password.<br />$msg")), $Mailer->GenerateHead("info@site.com"), false)->SendTo($email)->sendEmail();
							// If message sent successfully cast an appropriate message
							$errMsg	=	($sent->success)? "success":"error";
							include(NBR_RENDER_LIB."/assets/login/message.{$errMsg}.php");
							// Let further script know a message has been sent
							$messageinclude	=	true;
						}
				}
				
			// No message sent, show invalid
			if(!isset($messageinclude))
				include(NBR_RENDER_LIB.'/assets/login/message.invalid.php');
		}
	else
		include(NBR_RENDER_LIB.'/assets/login/message.reset.php');