<?php
// Load all functions associated with this action
\Nubersoft\nApp::call()->autoloadContents(__DIR__.DS.'..'.DS.'functions'.DS);
// See if there is a reply email address
$hasReply	=	nbr_getWebMaster();
$data		=	\Nubersoft\nApp::call()->toArray(\Nubersoft\nApp::call()->getRequest('data'));
$success	=	false;
$isAjax		=	\Nubersoft\nApp::call()->isAjaxRequest();

if(!empty($data['email']) && filter_var($data['email'],FILTER_VALIDATE_EMAIL))
	$success	=	true;

if(!$success) {
	if($isAjax)
		die(json_encode(array('success'=>false,'msg'=>'invalid email')));

	include(__DIR__.DS.'forgot_password.php');
}
else {
	$ID	=	false;
	if(filter_var(nbr_getEmailAddr($data['email'],$ID),FILTER_VALIDATE_EMAIL)) {
		$email		=	$data['email'];
		$run		=	nbr_validTimeStamp($email,true);
		$tempData	=	($run)? nbr_setTempPassword($email) : false;
		$encID		=	\Nubersoft\nApp::call('Safe')->encOpenSSL($ID);

		$msg		=	($run)? 'Please login in with this password: <a href="'.\Nubersoft\nApp::call()->getFunction('site_url').'?init=reset_password&token='.urlencode($encID).'">Reset</a> using: '.$tempData['password']:"You have already sent a password request change. Please wait at least an hour from your first request.";
		
		$returned['h']	=	($run)? "Email Sent Successfully.":"Hmm. This seems familiar somehow.";
		$returned['p']	=	($run)? "Your temporary password has been sent to you.":"Please wait an hour before resubmitting your email request.";
		$message	=	array(
						'h1'=>'Password Reset',
						'message'=>"This is an automated message for resetting your password.<br />$msg"
					);
		
		// Send email
		$Mailer	=	\Nubersoft\nApp::_EmailEngine();
		$sent	=	$Mailer	->setPrefs(
								"Password Reset",
								$Mailer->message($message),
								$Mailer->generateHead(WEBMASTER),
								false)
							->sendTo($email)
							->sendEmail();
		// If message sent successfully cast an appropriate message
		$errMsg	=	($sent->success)? "success":"error";
		\Nubersoft\nApp::call()->saveIncidental('preload',array('send_email_notice'=>$errMsg));
	}
}