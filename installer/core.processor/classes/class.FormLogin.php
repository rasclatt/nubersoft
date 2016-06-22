<?php
	class	FormLogin
		{
			public	static	$redirect;
			
			private	static	$Build;
			private	static	$validation;
			private	static	$form;
			private	static	$perms;
			
			private	function __construct()
				{
				}
				
			public	static	function buildForm($redirect = false)
				{
					if(empty(self::$validation))
						self::$validation	=	NBR_RENDER_LIB.'/assets/login/validation.php';

					if(empty(self::$form))
						self::$form			=	NBR_RENDER_LIB.'/assets/login/form.php';
						
					if(empty(self::$perms))
						self::$perms		=	NBR_RENDER_LIB.'/assets/form.bad.permissions.php';
					
					if(!isset(self::$Build))
						self::$Build	=	self::assemble($redirect);
						
					return self::$Build;
				}
			
			public	static	function addAttr($settings = false)
				{
					if(!empty($settings['validation']) && is_file($settings['validation']))
						self::$validation	=	$settings['validation'];
					
					if(!empty($settings['form']) && is_file($settings['form']))
						self::$form		=	$settings['form'];
						
					if(!empty($settings['perms']) && is_file($settings['perms']))
						self::$perms	=	$settings['perms'];
				}
			
			private	static	final function assemble($redirect = false)
				{
					ob_start();
					
					nApp::autoload('compare');
					$nubquery		=	nQuery();
					self::$redirect	=	$redirect;
					// Include validation js
					include_once(self::$validation);
					// Process requests
					if(compare(nApp::getEngine('action'),'sign_up')) {
						$signup['username']		=	$_POST['username'];
						$signup['first_name']	=	$_POST['first_name'];
						$signup['last_name']	=	$_POST['last_name'];
						$signup['email']		=	$_POST['email'];
						
						// Check for username
						$userValidate			=	$nubquery	->select("ID")
																->from("users")
																->where(array("username"=>$signup['username'],"email"=>$signup['email']),false,false,'or')
																->fetch();
						
						$in_syst				=	($userValidate != 0);
						
						if(!$in_syst) {
							// If the user is not in the system and the email is valid
							if(filter_var($signup['email'], FILTER_VALIDATE_EMAIL)) {
								nApp::autoload("FetchUniqueId");
								$pass					=	trim($_POST['password']);
								$hash					=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT)
																						->encrypt_password($pass)
																						->get_hash();
								$signup['password']		=	$hash;
								$signup['unique_id']	=	FetchUniqueId(rand(100,999));
								$signup['usergroup']	=	3;
								$signup['page_live']	=	'on';
								// Get columns
								$columns				=	array_keys($signup);
								// Sort columns
								asort($columns);
								// Sort values
								ksort($signup);
								// Write into page
								$nubquery	->insert("users")
											->columnsValues($columns,$signup)
											->write();
															
								$userValidate	=	$nubquery	->select("ID")
																->from("users")
																->where(array("username"=>$signup['username'],"email"=>$signup['email']),false,false,'or')
																->fetch();
								
								if($userValidate !=0) {
									$apimessage	=	'';
									// See if there is an api table
									nApp::autoload('get_tables_in_db');
									if(in_array("api",get_tables_in_db())) {
										$apikey		=	date("YmdHis").md5($signup['username']);
										// if so, auto-api key
										$nubquery	->insert("api")
													->columnsValues(array("unique_id","username","apikey"),array("unique_id"=>FetchUniqueId(),"username"=>$signup['username'],"apikey"=>$apikey))
													->write();
										$apimessage	=	" Your API Key is: $apikey";
									}
								
									include(NBR_RENDER_LIB.'/assets/register.success.php');
									
									$Mailer	=	new EmailEngine();
									$sent	=	$Mailer	->SetPrefs("Sign-up", $Mailer->Message(array('h1'=>'Thank you for registering!','message'=>"This is an automated message sent because this email address has been used to set up an account at ".$_SERVER['HTTP_HOST'].$apimessage)), $Mailer->GenerateHead(WEBMASTER), false)
														->SendTo($signup['email'])
														->sendEmail();
								}
							}
						}
						else {
?>								<div class="nbr_large_alert">
									Sorry. This user is already in the system.
								</div>	
<?php					}
					}
					
					if(!nApp::getUser('loggedin')) {
						global	$_incidental;
						include_once(self::$form);
					}
					else {
						if(nApp::getUser('usergroup') > nApp::getPage('usergroup')) {
							$error	=	"PERMISSION DENIED.";
							// Logged in, but no enough permissions
							include_once(self::$perms);
						}
					}
					
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
			
		}