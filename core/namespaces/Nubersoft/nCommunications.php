<?php
namespace Nubersoft;

class nCommunications extends nApp implements \Nubersoft\Communicator
	{
		protected	$to,
					$message,
					$from,
					$subject = false;
					
		public	function send()
			{
				if(empty($this->to))
					throw nException('Requires an email address to send email.');
				
				if(empty($this->from))
					$this->from	=	(defined('WEBMASTER'))? 'From: '.WEBMASTER."\r\n" : "From: no-reply@".$_SERVER['HTTP_HOST']."\r\n";
				
				return mail(implode(',',$this->to),$this->subject,$this->message,$this->from);
			}
			
		public	function addTo($content)
			{
				if(!is_array($content))
					$content	=	array($content);
				foreach($content as $key => $value) {
					if(!filter_var(trim($value),FILTER_VALIDATE_EMAIL))
						unset($content[$key]);
				}
				
				if(!empty($content))
					$this->to	=	(!empty($this->to) && is_array($this->to))? array_merge($this->to,$content) : $content;
					
				return $this;
			}
			
		public	function addFrom($content)
			{
				$this->from	=	'From: '.$content."\r\n";
				return $this;
			}
			
		public	function addMessage($content)
			{
				$this->message	=	$content;
				return $this;
			}
			
		public	function addSubject($content)
			{
				$this->subject	=	$content;
				return $this;
			}
		/*
		**	@description	Listens for a code request to login in the Admin section
		*/
		public	function observer()
			{
				# If there is a post
				if($this->getPost('action') != 'nbr_check_admin_code')
					return;
				# Set default
				$POST		=	array();
				# Parse the incoming form
				parse_str(urldecode($this->safe()->decode($this->getPost('deliver')->formData)),$POST);
				# Assign the token
				$token		=	(!empty($POST['token']['nProcessor']))? $POST['token']['nProcessor'] : false;
				# Assign the form fields
				$carrier	=	$POST['carriers'];
				$mobile		=	$POST['mobile'];
				$email		=	$POST['email'];
				$password	=	$POST['password'];
				# Get the token from the session
				$getToken	=	(!empty($this->getSession('token')->nProcessor->page))? $this->getSession('token')->nProcessor->page : false;
				# Check if user exists
				$user		=	$this->getHelper('UserEngine')->getUser($email,false);
				# Clear sailing by default
				$break		=	false;
				# If no token, stop
				if(empty($token))
					$break	=	'Token Invalid';
				# If token not sent, stop
				elseif(empty($getToken))
					$break	=	'Token Invalid';
				# If tokens don't match, stop
				elseif(($getToken != $token))
					$break	=	'Tokens Invalid';
				# If the user doesn't exist
				if($user == 0)
					$break	=	'User Invalid';
				# If the mobile is empty
				if(empty($mobile))
					$break	=	'Invalid Mobile';
				# If the mobile is not a number
				if(!is_numeric($mobile))
					$break	=	'Invalid Mobile';
				# If the mobile sms is not a valid email address
				if(!filter_var($mobile.$carrier,FILTER_VALIDATE_EMAIL))
					$break	=	'Invalid Mobile';
				# Stop if any conditions were met
				if($break)
					$this->ajaxResponse(array('alert'=>$break));
				# If not admin, stop
				if(!$this->isAdmin($user[0]['usergroup']))
					$this->ajaxResponse(array('alert'=>'Invalid Username or Password'));
				# Try logging in
				$valid	=	PasswordGenerator::Engine()->verify($password,$user[0]['password'])->isValid();
				# If invalid, stop
				if(empty($valid))
					$this->ajaxResponse(array('alert'=>'Invalid Username or Password'));
				# Create a code
				$code		=	$this->getCode();
				# Create a messaging for emailing
				$success	=	$this->addMessage('Your code from '.$this->siteUrl().': '.$code)
					->addTo($mobile.$carrier)
					->addTo($user[0]['email'])
					->send();
				# If the sms was successfully sent, let user know
				if($success)
					$this->setSession('login_temp_code',array('code'=>$code,'user'=>$user[0]),true);
				else
					$this->getHelper('nSessioner')->destroy('login_temp_code');
				# Return the code form back to the user
				$html	=	($success)? array('html'=>array($this->render($this->getHelper('nRender')->getBackEnd('code.form.php'))),'sendto'=>array('.nbr_general_form')) : array();
				# Create ajax response either way
				$this->ajaxResponse(array_merge($html,array('alert'=>"Message sent ".(($success)? 'successfully' : 'unsuccessfully').'.')));
			}
			
		protected function getCode()
			{
				$code	=	str_split(md5(mt_rand().$this->fetchUniqueId()));
				shuffle($code);
				return substr(implode($code),0,8);
			}
	}