<?php
namespace Nubersoft;

class	Saline
	{
		protected	static	$BcryptEngine,
							$PasswordHash,
							$PasswordEngine;
		
		private		static	$username,
							$password;
		
		public	static function verify($username = false, $password = false, $_return =  false)
			{
				self::$username	=	$username;
				self::$password	=	$password;
				
				if(!empty(self::$username) && !empty(self::$password)) {
					// Password hashing / Bcrypt takes a bit of time to execute
					set_time_limit(10);
					$nubquery	=	nApp::call()->nQuery();
					// Fetch username from database
					$result	=	$nubquery	->select()
											->from("users")
											->where(array("username"=>self::$username,'user_status'=>'on'))
											->fetch();
											
					// If no such user, return false
					if($result == 0)
						return false;
					// Check password_hash algo
					self::$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
					$validate				=	self::$PasswordEngine	->setUser(self::$username)
																		->verifyPassword(self::$password,$result[0]['password'])
																		->isValid();
					// If false, try with bcrypt
					if(!$validate) {
						self::$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::BCRYPT);
						$validate				=	self::$PasswordEngine	->setUser($username)
																			->verifyPassword(self::$password,$result[0]['password'])
																			->isValid();
					}
					// If false, check junk password
					if(!$validate)
						$validate	=	self::old_school($result);
						
					// If users found
					if($validate) {
						$_populate[]	=	'password';
						$_populate[]	=	'page_live';
						$_populate[]	=	'core_setting';
						$_populate[]	=	'page_order';
						
						if(!$_return) {
							session_regenerate_id(true);
							foreach($result[0] as $key => $value) {
								if(!in_array($key,$_populate)) {
									if($key == 'usergroup') {
										$groupNumeric				=	nApp::call()->convertUserGroup($value);
										$_SESSION[$key]				=	$groupNumeric;
										$_SESSION['usergroup_data']	=	array(
											'numeric' => $groupNumeric,
											'name' => $value
											);
									}
									else {
										$_SESSION[$key]	=	nApp::call('Safe')->encode($value);
									}
								}
								
								// Legacy includes
								if($key == 'ID') {
									$_SESSION['client_id']	=	nApp::call('Safe')->encode($value);
									$_SESSION['user_id']	=	nApp::call('Safe')->encode($value);
								}
							}
							
							nApp::call()->removeDataNode('_SESSION');
							nApp::call()->getHelper('Submits')->setSessionGlobal();
							return true;
						}
							
						return $result;
					}
					else
						$log	=	array('success'=>false,"error"=>'Username/Password Invalid',"type"=>"mismatch");
				}
				else
					$log	=	array('success'=>false,"error"=>'Username/Password Invalid',"type"=>"empty");
				
				nApp::call()->saveIncidental('login',$log);
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
						self::$PasswordEngine	->setUser($result[0]['username'])
												->hashPassword(self::$password)
												->write();
						return true;
					}
				}

				return false;
			}
	}