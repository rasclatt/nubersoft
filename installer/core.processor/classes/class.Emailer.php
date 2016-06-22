<?php
	
	class	Emailer
		{
			public		$sent;
			public		$response;
			public		$sending;
			
			protected	$addresses;
			protected	$headers;
			protected	$reply;
			protected	$notification;
			
			public	function __construct()
				{
					if(!defined('WEBMASTER'))
						define('WEBMASTER',"no-reply@".$_SERVER['HTTP_HOST']);
	
					if(!function_exists('check_empty'))
						AutoloadFunction('check_empty');
				}
			
			public	function AddTo($settings = array())
				{
					// This is the to address for the primary recipient
					if(!isset($this->sending['primary']['to']))
						$this->sending['primary']['to']	=	$this->MailingList($settings);
					
					return $this;
				}
				
			public	function AddFrom($settings = array())
				{
					// This is the from address for the primary recipient
					if(!isset($this->sending['primary']['from'])) {
							$this->sending['primary']['from']	=	$this->MailingList($settings);
							$this->addresses['From']			=	$this->sending['primary']['from'];
						}
					
					return $this;
				}
				
			public	function AddCc($settings = array())
				{
					// These will send with the primary email
					if(!isset($this->sending['primary']['Cc'])) {
							$this->sending['primary']['Cc']	=	$this->MailingList($settings);
							$this->addresses['Cc']			=	$this->sending['primary']['Cc'];
						}
					
					return $this;
				}
			
			public	function AddBcc($settings = array())
				{
					// These will send with the primary email
					if(!isset($this->sending['primary']['BCc'])) {
							$this->sending['primary']['BCc']	=	$this->MailingList($settings);
							$this->addresses['BCc']				=	$this->sending['primary']['BCc'];
						}
					
					return $this;
				}
			
			public	function AddSubject($subject = "Automated Message")
				{
					$this->sending['primary']['subject']	=	$subject;
					return $this;
				}

			public	function MailingList($settings = array())
				{
					if(!is_array($settings) && !empty($settings))
						$settings	=	array($settings);
						
					if(is_array($settings) && !empty($settings)) {
							foreach($settings as $proper => $address) {
									$address	=	trim($address);
									if(filter_var($address, FILTER_VALIDATE_EMAIL)) {
											
											$proper	=	(!is_numeric($proper))? ucwords(preg_replace("/[^0-9a-z\s]/i","",$proper)):"";
											$arr[]	=	(!is_numeric($proper) && !empty($proper))? $proper.'<'.$address.'>':$address;
										}
								}
						}

					// Assign To address
					return (isset($arr))? implode(",",$arr)."\r\n":false;
				}

			public	function ReturnMessage($settings = array())
				{
					if(!isset($settings['to']) || (isset($settings['to']) && !filter_var($settings['to'], FILTER_VALIDATE_EMAIL)))
						return $this;
					
					$email		=	$settings['to'];
					$subject	=	(isset($settings['subject']) && !empty($settings['subject']))? $settings['subject'] : "Automated Message";
					$message	=	(isset($settings['message']) && !empty($settings['message']))? str_replace(array("[","]","~","{","}"),"",$settings['message']) : "Message Blank";
					$template	=	((isset($settings['template']) && $settings['template'] == true) || !isset($settings['template']))? true:false;
					$include	=	((isset($settings['include']) && !empty($settings['include'])) && is_file($settings['include']))? $settings['include']:NBR_RENDER_LIB.'/assets/html/template.email.php';
					
					$this->sending['secondary']['to']		=	$email;
					$this->sending['secondary']['subject']	=	$subject;
					$this->sending['secondary']['template']	=	$template;
					$this->sending['secondary']['include']	=	$include;
					
					ob_start();
					if(is_file($include) && $template == true)
						include($include);
					else
						echo $message;

					$data	=	ob_get_contents();
					ob_end_clean();
					
					AutoloadFunction('apply_markup');
					$this->sending['secondary']['message']	=	use_markup($data);
						
					return $this;
				}
			
			public	function AddMessage($settings = array())
				{
					$email		=	$this->sending['primary']['to'];
					$subject	=	(!empty($this->sending['primary']['subject']))? $this->sending['primary']['subject'] : "Automated Message";
					$message	=	(!empty($settings['message']))? str_replace(array("[","]","~","{","}"),"",$settings['message']):"You have a question from ".strip_tags($email)." which was left empty.";
					$template	=	((isset($settings['template']) && $settings['template'] == true) || !isset($settings['template']));
					$include	=	(!empty($settings['include']))? $settings['include']:NBR_RENDER_LIB.'/assets/html/template.email.php';
					
					$this->sending['primary']['template']	=	$template;
					$this->sending['primary']['include']	=	$include;
					
					ob_start();
					if(is_file($include) && $template == true)
						include($include);
					else
						echo $message;

					$data	=	ob_get_contents();
					ob_end_clean();
					
					AutoloadFunction('use_markup');
					$this->sending['primary']['message']	=	use_markup($data);
					
					return $this;
				}
			
			public	function Compile($settings = array("type"=>'html'))
				{
					$this->headers	=	'';
					if(isset($this->addresses) && !empty($this->addresses)) {
							foreach($this->addresses as $kind => $list) {
									$this->headers	.=	$kind.": ".$list;
								}
						}
					
					if(!isset($settings['type']) || isset($settings['type']) && $settings['type'] == 'html') {
							$this->headers	.=	'MIME-Version: 1.0' . "\r\n";
							$this->headers	.=	'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						}
						
					return $this;
				}
			
			public	function Send($reply = false)
				{
					$subject	=	(isset($this->sending['primary']['subject']) && !empty($this->sending['primary']['subject']))? $this->sending['primary']['subject']:$this->AddSubject()->$this->sending['primary']['subject'];
					$message	=	(isset($this->sending['primary']['message']) && !empty($this->sending['primary']['message']))? $this->sending['primary']['message']:$this->AddMessage()->$this->sending['primary']['message'];
					$head		=	(isset($this->headers) && !empty($this->headers))? $this->headers:$this->Compiler()->headers;
					
					$this->response	=	false;
					
					if(!empty($this->sending['primary']['to'])) {
							if(mail($this->sending['primary']['to'], $subject, Safe::decode($message), $this->headers)) {
									$this->response['primary']['sent']		=	true;
									$this->response['primary']['subject']	=	$this->sending['primary']['subject'];

									if($reply && (isset($this->sending['secondary']['to']) && !empty($this->sending['secondary']['to']))) {
											if(!filter_var($this->sending['secondary']['to'],FILTER_VALIDATE_EMAIL))
												return $this;
											
											if(isset($this->sending['secondary']['subject']))
												$this->sending['secondary']['subject']	=	"Online Submission";
											
											$replyheaders	=	'MIME-Version: 1.0' . "\r\n";
											$replyheaders	.=	'Content-type: text/html; charset=iso-8859-1'.PHP_EOL;
											$replyheaders	.=	"From: ".$this->sending['primary']['to'].PHP_EOL;
											
											$replymessage	=	(!isset($this->sending['secondary']['message']))? $this->ReturnMessage()->$this->sending['secondary']['message']:$this->sending['secondary']['message'];
		
											if(mail($this->sending['secondary']['to'], $this->sending['secondary']['subject'], Safe::decode($replymessage), $replyheaders)) {
													$this->response['secondary']['sent']	=	true;
													$this->response['secondary']['subject']	=	$this->sending['secondary']['subject'];
												}
											else {
													$this->response['secondary']['sent']	=	false;
													$this->response['secondary']['subject']	=	$this->sending['secondary']['subject'];
												}
										}
								}
							else {
									$this->response['primary']['sent']		=	false;
									$this->response['primary']['subject']	=	$this->sending['primary']['subject'];	
								}
						}

					return $this;
				}
			
			public	function getSendStatus()
				{
					$array['primary']	=	(isset($this->response['primary']['sent']))? $this->response['primary']['sent'] : 'not set';
					$array['secondary']	=	(isset($this->response['secondary']['sent']))? $this->response['secondary']['sent'] : 'not set';
					
					return $array;
				}
		}