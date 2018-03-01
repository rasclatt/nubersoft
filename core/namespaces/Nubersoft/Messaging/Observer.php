<?php
namespace Nubersoft\Messaging;

class Observer extends \Nubersoft\nApp
{
	public	function listen()
	{
		switch($this->getRequest('action')){
			case('nbr_send_email'):
				$this->sendEmail();
		}
	}
	
	public	function sendEmail()
	{
		$args		=	func_get_args();
		$POST		=	$this->toArray($this->getPost());
		$token		=	(!empty($POST['token']['nProcessor']))? $POST['token']['nProcessor'] : false;
		
		if($token != $this->getHelper('nToken')->getPageToken()) {
			$this->toMsgAlert('Security token does not exist. Reload the page to get a new security token.','contact_emailing');
			return false;
		}
		
		if(!empty($args[0]) && is_object($args[0])) {
			$class		=	$args[0];
			$success	=	$class($POST)->send();
		}
		else {
			$Emailer	=	$this->getHelper('Emailer');
			$email		=	(!empty($POST['email']))? $POST['email'] : false;
			$subject	=	(!empty($POST['subject']))? strip_tags($POST['subject']) : false;
			$message	=	(!empty($POST['message']))? strip_tags($POST['message']) : false;
			$from		=	(!empty($POST['from']))? strip_tags($POST['from']) : WEBMASTER;
			$filter	=	[
				'email' => filter_var($email,FILTER_VALIDATE_EMAIL),
				'subject' => (!empty($subject)),
				'message' => (!empty($message)),
				'from' => (!empty($from))
			];

			foreach($filter as $key => $val) {
				if(empty($val)) {
					$this->toMsgAlert(ucfirst($key).' is not valid.','contact_emailing');
					return false;
				}
			}

			$success	=	$Emailer->addTo($email)
								->addFrom($from)
								->addSubject($subject)
								->useHtml()
								->addMessage($message)
								->send();
		}
		
		if(!$success)
			$this->toMsgAlert('Email could not be sent.','contact_emailing');
		else
			$this->toMsgSuccess('Email sent successfully.','contact_emailing');
	}
}