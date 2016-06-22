<?php
namespace Nubersoft;

class	UserEngine
	{
		private	static	$singleton;
		public	function __construct()
			{
				if(!empty(self::$singleton))
					return self::$singleton;
					
				self::$singleton	=	$this;
			}
		
		public	function loginUser($array = array())
			{
				$username	=	(!empty($array['username']))? $array['username'] : 'guest'.mt_rand().date('YmdHis');
				$usergroup	=	(!empty($array['usergroup']))? $array['usergroup'] : NBR_WEB;
				$fName		=	(!empty($array['first_name']))? $array['first_name'] : 'Guest';
				$lName		=	(!empty($array['last_name']))? $array['last_name'] : 'User';
					
				foreach($array as $key => $value) {
					$settings[$key]	=	$value;
				}
				
				$settings['usergroup']	=	$usergroup;
				$settings['username']	=	$username;
				$settings['first_name']	=	$fName;
				$settings['last_name']	=	$lName;
				// Get the session engine
				$nSession				=	new Sessioner();
				// Make the array a session
				$nSession->makeSession($nSession);
			}
		
		public	function logInUserWithCreds($username = false,$password = false)
			{
				if(empty($username) || empty($password))
					return false;
					
				$result	=	$this->getUser($username);
				
				if($result == 0)
					return false;
				
				// Check password_hash algo
				$PasswordEngine	=	\PasswordGenerator::Engine(\PasswordGenerator::USE_DEFAULT);
				$validate		=	$PasswordEngine	->set_user($username)
													->verify_password($password,$result[0]['password'])
													->valid;
				// If false, try with bcrypt
				if(!$validate) {
					$PasswordEngine	=	\PasswordGenerator::Engine(\PasswordGenerator::BCRYPT);
					$validate		=	$PasswordEngine	->set_user($username)
														->verify_password($password,$result[0]['password'])
														->valid;
				}
			}
			
		public	function getUser($username)
			{
				if(empty($username))
					return 0;
					
				return \nApp::con()	->query("select * from `users` where `username` = :0",array($username))
									->getResults();
			}
	}