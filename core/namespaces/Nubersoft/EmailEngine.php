<?php

class EmailEngine
	{
		public		$sender;
		public		$recipient;
		public		$field;
		public		$headermsg;
		public		$success;
		
		protected	$message;
		
		private		$subject;
		private		$to;
		private		$from;
		private		$ret_to_sender;
		private		$email_list;
		private		$return_address;
		private		$message_array;
		
		public	function __construct()
			{
				
			}
		
		public	function SetPrefs($subject, $message, $headermsg, $ret_to_sender)
			{
				$this->subject			=	$subject;
				$this->message			=	$this->Message($message);
				$this->headermsg		=	$headermsg;
				$this->ret_to_sender	=	$ret_to_sender;
				
				return $this;
			}
		
		public function spamCheck($field)
			{
				$this->field		=	filter_var($field, FILTER_SANITIZE_EMAIL);
				
				if(filter_var($this->field, FILTER_VALIDATE_EMAIL))
					return TRUE;
				else
					return FALSE;
			}
		
		public	function	Message($message_array)
			{
				$this->message_array	=	$message_array;
				
				if(is_array($this->message_array)) {
						$format	=	'
					<div style="width: 80%; display: inline-block; margin: 30px auto;
					padding: 2%; text-align: left; font-family:
					Gotham, \'Helvetica Neue\', Helvetica, Arial, sans-serif;">';
						if(isset($this->message_array['h1']))
							$format		.=	'<h1>'.wordwrap($this->message_array['h1'],60).'</h1>';
							$format		.=	'<p>'.wordwrap($this->message_array['message'],60).'</p>
					</div>';
					}
				else
					$format		=	$this->message_array;
					
				return $format;
			}
		
		public	function SendTo($address)
			{
				$this->sender	=	$address;
				return $this;
			}
		
		public	function GenerateHead($return_address)
			{
				$this->return_address	=	$return_address;
				
				$header					=	'MIME-Version: 1.0' . "\r\n";
				$header					.=	'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$header					.=	"From: ".$this->return_address." \r\n";
				
				if(!empty($this->headermsg) && ($this->headermsg !== false && $this->headermsg !== 0))
					$header		.=	$this->headermsg;
					
				return $header;
			}
		
		public function sendEmail()
			{
				// Send to user
				if(mail($this->sender, "Subject: " . $this->subject, $this->message, $this->headermsg))
					$this->success	=	true;
				else
					$this->success	=	false;
					
				return $this;
			}
		
		public function Send($to, $from)
			{
				$this->to		= $to;
				$this->from	= $from;
				
				if(isset($this->to) && !empty($this->to)) {
						if(is_array($this->to)) {
								foreach($this->to as $email_add) {
										if($this->spamCheck($email_add) == true)
											$emails['valid']['to'][]	=	$email_add;
									}
							}
						else {
								if($this->spamCheck($this->to) == true)
									$emails['valid']['to'][]	=	$this->to;
							}
							
						unset($email_add);
						
						$final_to		=	(count($emails['valid']['to']) > 1)? implode(", ",$emails['valid']['to']): $emails['valid']['to'][0];
						
						if(is_array($this->from)) {
								foreach($this->from as $email_add) {
										if($this->spamCheck($email_add) == true)
											$emails['valid']['from'][]	=	$email_add;
									}
							}
						else {
								if($this->spamCheck($this->from) == true)
									$emails['valid']['from'][]	=	$this->from;
							}
							
						$final_from		=	(count($emails['valid']['from']) > 1)? implode(", ",$emails['valid']['from']): $emails['valid']['from'][0];	
							
						$this->email_list['to']	=	$final_to;
						$this->email_list['from']	=	$final_from;
					}
				
				if(!empty($this->email_list['to']) && !empty($this->email_list['from'])) :
					if($this->ret_to_sender == true) {
							$combo_send	=	$this->email_list['to'].','.$this->email_list['from'];
							// Send copy to recipient
							mail($this->email_list['to'], $this->subject, $this->message, $this->GenerateHead($this->email_list['from'])."Cc: ".$this->email_list['from']." \r\n");
						}
					else {
							// Send to recipient
							mail($this->email_list['to'], $this->subject, $this->message, $this->GenerateHead($this->email_list['from']));
						}
				endif;
			}
	}
	 ?>