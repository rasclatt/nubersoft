<?php
namespace nWordpress;

use \nWordpress\Email\Template as Email;

class Observer extends \nWordpress\Automator\Observer
{
	public	function listen()
	{
		$this->setErrorMode(1);
		
		$Token		=	new Token();
		$timezone	=	get_option('timezone_string');
		$User		=	new User();

		if(!empty($timezone)) {
			date_default_timezone_set($timezone);
		}
		
		switch($this->getPost('action')){
			case('nbr_admin_send_login_request'):
				$POST	=	$this->toArray($this->getDataNode('_RAW_POST'));
				
				if(empty($POST['ntoken'])) {
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('Invalid Request'));
				}
				
				$token		=	$Token->create('nbr_set_token');
				$verified	=	$Token->verify($this->getPost('ntoken'), 'nbr_set_token');
				
				if(!$verified) {
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('InvalidRequest. Try again'));
				}
				$valid	=	$User->userValid($POST['username'],$POST['password'],'email');
				
				if(!$valid){
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('Username or password invalid'));
				}
				
				if(!in_array('administrator',$User->getUser()->roles)) {
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('You must be an admin'));
				}
				
				$Email	=	new Email();
				$phone	=	preg_replace('/[^\d]+/','',$this->getPost('phone'));
				if(is_numeric($phone)) {
					$Twilio	=	new \Beyond\Twilio();
					$token	=	$Token->create(strtolower($User->getUser()->user_email.'_'.date('YmdH')));
					$Twilio->send($phone,'Your code from '.site_url().': '.$token);
				}
				# Fetch template from the \nWordpress\Email\Template\renderlib folder
				$template	=	$Email->getTemplate('template-general.html',[
					'company' => site_url(),
					'message' => 'Your pass key is: '.$token,
					'title'=>'Temporary Admin Code'
				]);
				# Send email
				$success	=	$Email->setTo($User->getUser()->user_email)
					->setFrom(get_option('admin_email'))
					->setSubject('Question/Comment from '.site_url())
					->setMessage($template)
					->send();
				
				if($success) {
					$this->saveSetting('_GET',['success'=>true,'msg'=>'Your password has been emailed to you.'],true);
					$this->saveSetting('_POST',['event'=>'email_pass','success'=>true,'email'=>$User->getUser()->user_email],true);
				}
				else {
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('An error ocurred sending your password.'));
				}
				break;
				
			case('nbr_admin_use_login_request'):
				
				$email		=	$this->getPost('email');
				$pin		=	$this->getPost('pin');
				$token		=	$Token->create('nbr_set_token');
				$verified	=	$Token->verify($this->getPost('ntoken'), 'nbr_set_token');
				$verify		=	$Token->verify($pin,$email.'_'.date('YmdH'));
				
				if(!$verify) {
					wp_redirect($this->getDataNode('_SERVER')->REQUEST_URL.'?error=true&msg='.urlencode('Password was incorrect. Try again.'));
				}
				else {
					$user	=	$User->get($email,'email');
					wp_set_auth_cookie($user->ID);
					wp_redirect(admin_url());
				}
		}
	}
	
	public	function errorListener()
	{
		$Errors	=	new Reporting();
		
		switch($this->getGet('error')) {
			case('register'):
				echo $Errors->getErrorMsg($this->getGet('error'),'001');
				break;
			case('login'):
				echo $Errors->getErrorMsg($this->getGet('error'),'001');
				break;
			default:
				if($this->getGet('code')) {
					
					$err	=	$Errors->getRemoteMsg($this->getGet('error'), 'error', $this->getGet('code'));
					
					$finErr	=	(empty($err))? $Errors->setMsg('The cart is not available yet','checkout','001')->getErrorMsg($this->getGet('error'),$this->getGet('code')) : $err;
					
					if(empty($finErr)) {
						echo ($this->getGet('msg'))? urldecode($this->getGet('msg')) : 'An error occurred.';
					}
					else
						echo $finErr;
				}
				else {
					echo ($this->getGet('msg'))? urldecode($this->getGet('msg')) : 'An error occurred.';
				}
		}
	}
	
	public	function successListener()
	{
		$Success	=	new Reporting();
		
		if(empty($this->getGet('success')))
			return false;
		
		if($this->getGet('msg')) {
			echo urldecode($this->getGet('msg'));
		}
		else
			echo $Success->getRemoteMsg($this->getGet('success'),'success',$this->getGet('code'));
	}
}