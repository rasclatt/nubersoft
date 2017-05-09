<?php
/*Title: HeadProcessor*/
/*Description: This is the main processor*/
class HeadProcessor
	{
		public		$reset;
		
		protected	$payload,
					$nuber;
		
		private		$registry;
		
		public	function __construct()
			{
				$this->registry	=	nApp::getRegistry();
			}
		
		public	function Process($payload = false)
			{
				if(!nApp::siteValid()) {
					// Check if reinstall credentials
					$sCreds	=	nApp::getPost('save_credentials');
					if(!empty($sCreds) && is_admin()) {
						if(!nApp::tokenMatch('dbinstall'))
							return;
							
						include_once(NBR_RENDER_LIB.DS.'admintools'.DS.'classes'.DS.'installer'.DS.'create.creds.php');
						$_createCreds	=	MySQLCredentials::Create($_POST);
					}
					
					return;
				}
				
				$post_valid		=	(!empty($payload));
				$arr_valid		=	(is_array($payload) && $post_valid); 
				$this->payload	=	($arr_valid)? $payload : $_POST;
				$nubquery		=	nQuery();
				
				// die(printpre(nApp::getRequest()).printpre($_SESSION));
				// Process and rest token
				$setActDef	=	(!empty(\nApp::getRequest('action')))? \nApp::getRequest('action') : 'page';
				$allow		=	\nApp::nToken()->resetTokenOnMatch($_REQUEST,'nProcessor',$setActDef,mt_rand(1000,9999));
				if(!$allow)
					return;
				// Automate actions from config files
				$configs	=	\nApp::getConfigs();
				\nApp::nAutomator()	->listenFor('action','request')
									->organizeBy('name')
									->automate($configs);
				// If a post request has been made
				if(!empty($this->payload['requestTable']) && !empty($nubquery)) {
					//die(printpre($this->payload));
					AutoloadFunction('check_empty');
					$this->payload['requestTable']	=	(!empty($this->payload['requestTable']))? $this->payload['requestTable'] : nApp::getDefaultTable();
					// Reset the default toble to the table being injected
					nApp::resetTableAttr($this->payload['requestTable']);
					// If not logged in
					if(!is_loggedin()) {
						$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
						// Compare temp pass to stored temp pass
						if(check_empty($this->payload,'action','login-temp')) {									
							$this->matchTempCreds($PasswordEngine);
						}
						// If action is to update password
						elseif(check_empty($this->payload,'action','finalize-reset')) {
							$password	=	$PasswordEngine->encrypt_password($this->payload['password'])->get_hash();
							// Update
							$nubquery	->update("users")
										->set(array("password"=>$password,"timestamp"=>NULL,"reset_password"=>NULL))
										->where(array("ID"=>$this->payload['ID']))
										->write();
										
							// Pass an error message
							$_incidental['pass_reset']	=	"Password reset successfully.";
						}
					}
					// Process requests
					else {
						// If the pagebuilder is envoked run it
						if((check_empty($this->payload,'command','page_builder')) && is_admin()) {
							
							$build_dir	=	new PageBuilder();
							if(check_empty($this->payload,'action','reset')) {
								$all_menus	=	$nubquery	->select()
															->from("main_menus")
															->fetch();
															
								if($all_menus != 0) {
									foreach($all_menus as $menu)
										$build_dir->Update($menu);
								}
							}
							else
								$build_dir->execute($this->payload);			

							if(nApp::getRequest('jumppage'))
								$this->processJumpPage();

							header("Location: ".$_SERVER['HTTP_REFERER']);
							exit;
						}
						// All other envoke
						elseif(check_empty($this->payload,'command','settings')) {
							if(is_admin()) {
								if(check_empty($this->payload,'instruct','reset'))
									// Reset the system prefs
									$this->CommandInstructReset();
								elseif(check_empty($this->payload,'instruct','modify'))
									// Modify one of the system prefs															
									$this->CommandInstructModify();
								else { // Save prefs
									$this->ProcessPrefs();
									$this->CreateHtaccess();
								}
							}
							// Redirect back
							header("Location: ".$_SERVER['HTTP_REFERER']);
							exit;
						}
						elseif(check_empty($this->payload,'command','dup'))
							$this->CommandDup();
						$MySQLEngine	=	new DBWriter();
						$MySQLEngine->execute($this->payload);
					}
				}
			}
		
		protected	function CommandDup()
			{
				$nubquery						=	nQuery();
				$table							=	$this->payload['send_to'];
				$archived						=	$nubquery	->select()
																->from($table)
																->where(array("ID"=>$this->payload['ID']))
																->fetch();
																
				$this->payload					=	$archived[0];
				$this->payload['ID']			=	"";
				$this->payload['unique_id']		=	"";
				$this->payload['ref_spot']		=	"";
				$this->payload['requestTable']	=	$table;
				$this->payload['ref_page']		=	nApp::getPage('unique_id');
				if(isset($this->payload['page_live']))
					$this->payload['page_live']		=	"off";	
				$this->payload['add']			=	true;
			}
		
		protected	function CommandInstructReset()
			{
				AutoloadFunction('create_default_prefs');
				create_default_prefs();
			}
		
		protected	function CommandInstructModify()
			{
				AutoloadFunction('get_site_prefs');
				$siteprefs	=	Safe::to_array(nApp::getSitePrefs('site'));
				
				// Redo site prefs
				if(!$siteprefs) {
					AutoloadFunction('create_default_prefs');
					create_default_prefs('site');
					
					// Get all site prefs (convert)
					$siteprefs	=	Safe::to_array(nApp::getSitePrefs('site'));
				}
					
				if(!empty($this->payload['modkey'])) {
					AutoloadFunction('process_site_prefs');
					
					$modkey							=	$this->payload['modkey'];
					if(is_object($siteprefs['content'])) {
						$siteprefs['content']			=	(array) $siteprefs->content;
						$siteprefs['content'][$modkey]	=	$this->payload[$modkey];
					}
						
					$siteprefs['content']			=	json_decode($siteprefs->content);
					$siteprefs['update']			=	true;
					$siteprefs['requestTable']		=	'system_settings';
					
					// Write to database
					$MySQLEngine	=	new DBWriter();
					$MySQLEngine->execute($siteprefs);
				}
			}
		
		protected	function ProcessPrefs()
			{
				// Load the site variables processor
				AutoloadFunction('process_site_prefs');
				// Assign variables
				$this->payload	=	process_site_prefs();
				// Write to database
				$MySQLEngine	=	new DBWriter();
				$MySQLEngine->execute($this->payload);
			}
		
		protected	function CreateHtaccess()
			{
				$nubquery		=	nQuery();
				$htaccess_get	=	$nubquery	->select("content")
												->from("system_settings")
												->where(array("name"=>"settings","page_element"=>"settings_site"))
												->fetch();
				
				// Write htaccess file
				if($htaccess_get != 0) {
					try {
						$htaccess_conv	=	Safe::to_array(json_decode($htaccess_get[0]['content']));
						$htaccess		=	Safe::decode($htaccess_conv['htaccess']);
						$writeHTACCESS	=	SaveToDisk::Write(array("filename"=>'.htaccess',"payload"=>$htaccess,"write"=>'w'));
					}
					catch (Exception $e) {
						if(is_admin())
							echo printpre($e);
						else
							echo 'An error occurred creating htaccess file.';
						exit;
					}
				}
			}
		
		private	function matchTempCreds($PasswordEngine)
			{
				// Search for submitted email address
				$get_new	=	nQuery()	->select()
											->from("users")
											->where(array("email"=>$this->payload['email']))
											->fetch();
				// If found, continue
				if(!empty($get_new[0]['reset_password'])) {
						$pass			=	Safe::decode($this->payload['password']);
						$verified		=	$PasswordEngine->verify_password($pass,$get_new[0]['reset_password'])->valid;
						// If passwords match, assign data
						if($verified) {
							$this->reset	=	true;
							$this->data		=	$get_new[0];
							nApp::saveIncidental('pass_match',array("success"=>true));
							nApp::saveSetting("reset_user",$this->payload["email"]);
							// Pass an error message
							$_incidental['pass_match']	=	"Password match.";
						}
						else {
							nApp::saveIncidental('pass_match',array("success"=>false,"msg"=>"invalid"));
							// Pass an error message
							$_incidental['pass_match']	=	"Temporary password mis-match.";
						}
					}
				else {
					nApp::saveIncidental('pass_match',array("success"=>false,"msg"=>"error"));
					// Pass an error message
					$_incidental['pass_match']	=	"Invalid.";
				}
			}
		
		public	function Login()
			{
				$_setting['login']	=	(check_empty($_POST,'action','login'))? 1 : 0;
				$_setting['logout']	=	(check_empty($_REQUEST,'action','logout'))? 1 : 0;
				$_setting['remote']	=	(check_empty($_REQUEST,'action','login_remote'))? 1 : 0;
				$reload_page		=	false;
				// See if there is a remote login attempt (try if install is set)
				if($_setting['remote'] == 1 && nApp::tokenMatch('login_remote')) {
					$host	=	"http://www.nubersoft.com/api/index.php?service=Fetch.Account&action=login_remote&vals=".Safe::jSURL64($_POST);
					$cURL	=	new cURL($host);
					
					if(!empty($cURL->response['login']) && $cURL->response['login'] == 1) {
						$_SESSION['username']	=	$_POST['username'];
						$_SESSION['usergroup']	=	1;
						$_SESSION['first_name']	=	$_POST['username'];
						$reload_page			=	true;
					}
				}
				// Check token
				elseif($_setting['login'] == 1) {
					
					// If token exists for login
					if(nApp::nToken()->tokenExists('login')) {
						// If the token is empty, record and return							
						if(empty(nApp::getPost('token')->login)) {
							nApp::saveIncidental('login',array('bad_request'=>'Login token missing. Reload the page to try again.'));
							return;
						}
						// If the token is set, but does not match, just return
						AutoloadFunction("ValidateToken");
						if(!ValidateToken('login',nApp::getPost()->token->login)) {
							nApp::saveIncidental('login',array('bad_request'=>'Login token invalid. Reload the page to try again.'));
							return;
						}
						// If all goes well and matched, then clear the token
						nApp::nToken()->clearToken('login');
					}
					else
						return false;
				}	
				
				// Self-contained login
				if(array_sum($_setting) > 0) {
					// Destroy session
					if($_setting['logout']) {
						session_destroy();
						
						if(nApp::getPost('jumppage'))
							$this->processJumpPage();
						
						header("Location: ".str_replace("index.php","",$_SERVER['PHP_SELF']));
						exit;
					}
					else {	
						// See if the page is an administration page
						$is_admin	=	(is_admin());
						$username	=	nApp::getPost('username');
						if(!empty($username)) {
							AutoloadFunction("QuickWrite");
							// See if there is a login bypass file
							$_bypass	=	nApp::getBypass('login');
							$bfile		=	Safe::normalize_url(NBR_ROOT_DIR."/$_bypass");
							$_bypass	=	(is_file($bfile));
							// Write to log an attempt
							QuickWrite(array("data"=>"Token Pass: ".nApp::getPost("username").PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"login.txt","mode"=>"c+"));
							// If it's not an administration page and bypass is valid
							if($_bypass && !$is_admin)
								include_once($bfile);
							// Process the login normally
							else {
								// Check if the user is valid
								$user		=	nApp::getUserInfo(nApp::getPost('username'));
								// If user invalid, return
								if(!$user) {
									nApp::saveIncidental('login',array('success'=>false,'error'=>'invalid username/password'));
									return false;
								}
								// See if loading page is an admin page
								$aPage		=	(!empty(nApp::getPage()->is_admin))? nApp::getPage()->is_admin : false;								
								// If the referring page is not admin page
								if(!$aPage) {
									// If admin user valid
									if(nApp::adminCheck($user->usergroup)) {
										// Check if allow from any-page-admin-login is set
										$openAllow	=	(defined("OPEN_ADMIN") && OPEN_ADMIN);
										// If not allowed to log in except for admin page stop
										if(!$openAllow) {
											$msg	=	array('success'=>false,'error'=>'invalid username/password');
											nApp::saveIncidental('login',$msg);
											nApp::saveToLogFile(date('YmdHis').time().'.log',$msg);
											return false;
										}
									}
								}
								// Continue logging in
								processLogin::execute($_POST);
							}
						}
					}	
				}
				// If there is a jumppage, execute it
				if(nApp::getPost('jumppage'))
					$this->processJumpPage();
				// If 
				if($reload_page) {
					header("Location: " . str_replace("index.php","",$_SERVER['PHP_SELF']));
					exit;
				}
			}
			
		private	function processJumpPage()
			{
				if(nApp::getRequest('jumppage')) {
					$link	=	Safe::decOpenSSL(nApp::getRequest('jumppage'),array("urlencode"=>true));
					if(!preg_match('/^http/',$link))
						$link	=	site_url()."/".ltrim($link,'/');
					
					die($link);
					
					header("Location: {$link}");
					exit;
				}
			}
	}