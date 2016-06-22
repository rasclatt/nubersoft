<?php
	class	Saline
		{
			protected	static	$BcryptEngine;
			protected	static	$PasswordHash;
			protected	static	$PasswordEngine;
			
			private		static	$username;
			private		static	$password;
			
			public	static function Verify($username = false, $password = false, $_return =  false)
				{
					self::$username	=	$username;
					self::$password	=	$password;
					
					if(!empty(self::$username) && !empty(self::$password)) {
							// Password hashing / Bcrypt takes a bit of time to execute
							set_time_limit(10);
							AutoloadFunction('nQuery');
							$nubquery	=	nQuery();
							// Fetch username from database
							$result	=	$nubquery	->select()
													->from("users")
													->where(array("username"=>self::$username))
													->fetch();
													
							// If no such user, return false
							if($result == 0)
								return false;
							// Check password_hash algo
							self::$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
							$validate				=	self::$PasswordEngine	->set_user(self::$username)
																				->verify_password(self::$password,$result[0]['password'])
																				->valid;
							// If false, try with bcrypt
							if(!$validate) {
									self::$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::BCRYPT);
									$validate				=	self::$PasswordEngine	->set_user($username)
																						->verify_password(self::$password,$result[0]['password'])
																						->valid;
								}
							// If false, check junk password
							if(!$validate)
								$validate	=	self::old_school($result);
							// If there is a reset feature and it's set and up til now all attempts fail
							if(!empty($result[0]['reset_password']) && !$validate) {
									// Save default timezone
									// Assign timezone
									AutoloadFunction('get_timezone');
									date_default_timezone_set(get_timezone());
									// Assign times
									$now		=	strtotime("now");
									$expire		=	strtotime($result[0]['timestamp']." + 1 hour");
									// Check if the expiration is set
									$expired	=	($expire > $now)? false : true;
									// Save result to register
									$register	=	new RegisterSetting();
									// If the expired
									if($expired) {
											$nubquery	->update("users")
														->set(array("timestamp"=> '', "reset_password"=>''))
														->where(array("username"=>self::$username))
														->write();
														
											$register->UseData('password_reset',array("reset"=>false,"expired"=>true))->SaveTo("settings");
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
													$register->UseData('password_reset',array("reset"=>true,"expired"=>false))->SaveTo("settings");
												}
										}
								}
								
							// If users found
							if($validate) {
									$_populate[]	=	'password';
									$_populate[]	=	'page_live';
									$_populate[]	=	'core_setting';
									$_populate[]	=	'page_order';
									
									if(!$_return) {								
											foreach($result[0] as $key => $value) {
													if(!in_array($key,$_populate))
														$_SESSION[$key]	=	Safe::encode($value);
													
													// Legacy includes
													if($key == 'ID') {
															$_SESSION['client_id']	=	Safe::encode($value);
															$_SESSION['user_id']	=	Safe::encode($value);
														}
												}
												
											return true;
										}
										
									return $result;
								}
							else
								$log	=	array("type"=>"mismatch","msg"=>'Username/Password Mismatch');
						}
					else
						$log	=	array("type"=>"invalid","msg"=>'Username/Password Invalid');
					
					if(!isset($register))
						$register	=	new RegisterSetting();

					$register->UseData('login',$log)->SaveTo("incidentals");
					return false;
				}
			
			protected	static	function old_school($result = false)
				{
					if(empty($result))
						return false;
						
					// Check if new(er) password is a salted one or not
					if((strpos($result[0]['password'],".") !== false) && (strpos($result[0]['password'],'$') === false)) {
							$_break					=	explode(".",$result[0]['password']);
							$_pass					=	$_break[1];
							$_salt					=	$_break[0];
							$result[0]['password']	=	$_pass;
							self::$password			=	hash('sha512',$_salt.self::$password);
							// Indicate old passwords
							$oldpass				=	true;
						}
					// If not salted and not bycrpt
					elseif(strpos($result[0]['password'],'$') !== false)
						$oldpass	=	false;
					else
						$oldpass	=	true;
					
					// If it is an old password and password matches database
					// run a save to bcrypt
					if($oldpass) {
							if($result[0]['password'] == self::$password) {
									self::$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
									self::$PasswordEngine	->set_user($result[0]['username'])
															->encrypt_password(self::$password)
															->write();
									return true;
								}
						}

					return false;
				}
		}