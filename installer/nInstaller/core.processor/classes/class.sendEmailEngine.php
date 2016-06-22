<?php

	class sendEmailEngine
		{
			public		$sender;
			public		$recipient;
			private		$subject;
			protected	$message;
			public		$field;
			public		$headermsg;
			
			public function spamCheck($field)
				{
					$this->field		=	filter_var($field, FILTER_SANITIZE_EMAIL);
					
					if(filter_var($this->field, FILTER_VALIDATE_EMAIL))
						return TRUE;
					else
						return FALSE;
				}
				
			public function sendEmail($sender, $recipient, $subject, $message, $headermsg)
				{
					$this->sender		=	$sender;
					$this->recipient	=	$recipient;
					$this->subject		=	$subject;
					$this->message		=	$message;
					$this->headermsg	=	$headermsg;
					// Send to self a copy
				//	mail($this->recipient, "Subject: $subject", $this->message, $this->headermsg);
					
					// Send to user
					mail($this->sender, "Subject: $subject", $this->message, $this->headermsg);
				}
		} ?>