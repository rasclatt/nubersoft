<?php
	class processLogin
		{
			public	static	$request_array;
			public	static	$password_encode;
			public	static	$bypass_login;
			public	static	$bypass_file;
			
			public	static	function execute($request_array)
				{
					self::$request_array	=	$request_array;
					self::$password_encode	=	'sha512';
					// Remote login check
					$_remLogin				=	(nApp::getEngine('action') == 'login_remote');
					if((isset(self::$request_array['login']) || $_remLogin) && !empty(self::$request_array['username']) && !empty(self::$request_array['password'])) {
						// Only process login if user is not already logged in.
						if(!isset($_SESSION['usergroup'])) {
							$username	=	self::$request_array['username'];
							$password	=	self::$request_array['password'];
							// If the user is request to login in remotely
							if($_remLogin) {
								$_creds['username']	=	(!empty($_POST['username']))? $_POST['username']:0;
								$_creds['password']	=	(!empty($_POST['password']))? $_POST['password']:0;
								$_creds['apikey']	=	(!empty($_POST['apikey']))? $_POST['apikey']:0;
								$_creds['domain']	=	(!empty($_POST['domain']))? $_POST['domain']:0;
								if(!in_array('0',$_creds)) {
									// Login for guest user on first install
									$response	=	self::FetchRemote($_creds);
									if(isset($response['login']) && $response['login'] == 1) {
										$_SESSION['usergroup']	=	0;
										$_SESSION['username']	=	$_creds['username'];
										$_SESSION['first_name']	=	'Guest';
										$_SESSION['last_name']	=	'User';
										$_success				=	true;
									}
								}
									
								if(!isset($_success)) {
									global $_incidental;
									$_incidental['login']['mismatch']	=	'Username/Password/API';
								}
							}
							// Else login locally
							// Verify username and password
							else
								\Nubersoft\Saline::Verify($username,$password);
						}
					}

					// Update the nubersoft user credentials
					\nApp::saveSetting('user',array(
						'loggedin'=>(isset($_SESSION['usergroup'])),
						'usergroup'=>((isset($_SESSION['usergroup']))? (int) $_SESSION['usergroup']: false),
						'admin'=>is_admin(),
						'admission'=>(is_loggedin() && !is_admin())
					));
					/*
					NubeData::$settings->user->loggedin		=	(isset($_SESSION['usergroup']));
					NubeData::$settings->user->usergroup	=	(isset($_SESSION['usergroup']))? (int) $_SESSION['usergroup']: false;
					NubeData::$settings->user->admin		=	is_admin();
					NubeData::$settings->user->admission	=	(is_loggedin() && !is_admin());
					*/
					$userset	=	(isset($_SESSION['username']))? $_SESSION['username'] : false;
					\nApp::nFunc()->autoload('nbr_fetch_error',NBR_FUNCTIONS);
					$nLogger	=	new \Nubersoft\nLogger();
					// Write to log an attempt
					$nLogger->ErrorLogs_Login(nbr_fetch_error('login',__FILE__,__LINE__));
				}

			public	static function login($request_array)
				{
					register_use(__METHOD__);
					
					self::$request_array		=	$request_array;
					self::$password_encode		=	(!empty(self::$password_encode))? self::$password_encode: 'sha512';

					// Saved settings for the mysql credentials
					if(self::check_bypass()) {
						if(self::$bypass_login !== false) 
							$_bypassed	=	true;
					}

					if(isset($_bypassed))
						include(self::$bypass_file);
					else
						self::execute(self::$request_array, self::$password_encode);
				}
			
			public	static function check_bypass()
				{
					register_use(__METHOD__);
					$nuber	=	nQuery();
					
					if($nuber) {
						//Check to see if the login is bypassed
						$bypass	=	$nuber	->select("`content`")
											->from("system_settings")
											->where(array("component"=>'bypass',"name"=>'login',"page_live"=>'on'))
											->fetch();
						if($bypass != 0) {
							$bypass_add			=	$bypass[0];
							$bypassed			=	NBR_ROOT_DIR.$bypass_add['content'];
							
							self::$bypass_login	=	(is_file($bypassed));
							self::$bypass_file	=	(self::$bypass_login)? $bypassed:false;
						}
						else
							self::$bypass_login	=	false;
					}
					
					return (isset(self::$bypass_login))? self::$bypass_login:false;
				}
				
			protected static function	FetchRemote($_creds = array())
				{
					register_use(__METHOD__);
					AutoloadFunction("check_empty");
					if(!empty($_creds) && check_empty($_POST,'action','login_remote')) { 
						$_remote	=	'http://www.nubersoft.com/api/index.php?service=Fetch.Account&action=login_remote&username='.urlencode($_creds['username']).'&password='.urlencode($_creds['password']).'&apikey='.urlencode($_creds['apikey']).'&domain='.urlencode($_creds['domain']);
						
						$connect	=	new cURL($_remote);
						
						return $connect->response; 
					}
				}
		}