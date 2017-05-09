<?php
/*Title: class CreateUser*/
/*Description: This small class checks the payload for a username and password combination. If there, it will encrpt it.*/
	class	CreateUser
		{
			public		$payload;
			protected	$encrypt;
			
			public	function check($payload = array())
				{
					$this->payload	=	$payload;
					
					if(isset($this->payload['username']) && isset($this->payload['password'])) {
							if(!empty($this->payload['username']) && !empty($this->payload['password'])) {
									$this->encrypt	=	(isset($this->payload['add']) || (isset($this->payload['update']) && (isset($this->payload['delete']) && $this->payload['delete'] == 'on' || !isset($this->payload['delete']))));
								}
							else {
									if(empty($this->payload['username']))
										unset($this->payload['username']);
										
									if(empty($this->payload['password']))
										unset($this->payload['password']);
								}
						}
					
					return $this;
				}
			
			public	function execute($_lead = false)
				{
					if($this->encrypt) {
							if($_lead) {
									// New password hashing (password_has or blowfish)
									$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
									// hash the password
									$this->payload['password']	=	$PasswordEngine->encrypt_password($this->payload['password'])->get_hash();
								}
						}
						
					return	$this;
				}
		} ?>