<?php
namespace nPlugins\Nubersoft;

class	FormLogin extends \Nubersoft\nRender
	{
		public	static	$redirect;
		
		private	static	$Build,
						$validation,
						$form,
						$perms;
		
		public	function buildForm()
			{
				if($this->getIncidentals('login'))
					$this->getTemplatePlugin('message_invalid');
			}
		
		private	final function assemble($redirect = false)
			{
				ob_start();
				$nubquery		=	$this->nQuery();
				self::$redirect	=	$redirect;
				// Include validation js
				include_once(self::$validation);
				// Process requests
				if($this->compare(self::call()->getEngine('action'),'sign_up')) {
					$signup['username']		=	$_POST['username'];
					$signup['first_name']	=	$_POST['first_name'];
					$signup['last_name']	=	$_POST['last_name'];
					$signup['email']		=	$_POST['email'];
					
					// Check for username
					$userValidate			=	$nubquery	->select("ID")
															->from("users")
															->where(array("username"=>$signup['username'],"email"=>$signup['email']),false,false,'or')
															->getResults();
					
					$in_syst				=	($userValidate != 0);
					
					if(!$in_syst) {
						// If the user is not in the system and the email is valid
						if(filter_var($signup['email'], FILTER_VALIDATE_EMAIL)) {
							self::call()->autoload("fetch_unique_id");
							$pass					=	trim($_POST['password']);
							$hash					=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT)
																					->hashPassword($pass)
																					->getHash();
							$signup['password']		=	$hash;
							$signup['unique_id']	=	fetch_unique_id(rand(100,999));
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
															->getResults();
							
							if($userValidate !=0) {
								$apimessage	=	'';
								// See if there is an api table
								self::call()->autoload('get_tables_in_db');
								if(in_array("api",get_tables_in_db())) {
									$apikey		=	date("YmdHis").md5($signup['username']);
									// if so, auto-api key
									$nubquery	->insert("api")
												->columnsValues(array("unique_id","username","apikey"),array("unique_id"=>fetch_unique_id(),"username"=>$signup['username'],"apikey"=>$apikey))
												->write();
									$apimessage	=	" Your API Key is: $apikey";
								}
							
								include(NBR_RENDER_LIB.DS.'assets'.DS.'register.success.php');
								
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
				
				if(!self::call()->getUser('loggedin')) {
					global	$_incidental;
					include_once(self::$form);
				}
				else {
					if(self::call()->getUser('usergroup') > self::call()->getPage('usergroup')) {
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