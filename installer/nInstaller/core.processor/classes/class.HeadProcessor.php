<?php
/*Title: HeadProcessor*/
/*Description: This is the main processor*/
	class HeadProcessor
		{
			public		$reset;
			
			protected	$payload;
			protected	$nuber;
			
			private		$registry;
			
			public	function __construct()
				{
					register_use(__METHOD__);
					AutoloadFunction('nQuery,site_valid');
					$this->registry	=	nApp::getRegistry();
				}
			
			public	function Process($payload = false)
				{
					register_use(__METHOD__);
					if(!nApp::siteValid()) {
						// Check if reinstall credentials
						if((!empty($_POST['save_credentials'])) && is_admin()) {
							AutoloadFunction('TokenMatch');
							
							if(!TokenMatch(array("token_name"=>"dbinstall")))
								return;
								
							include_once(RENDER_LIB.'/admintools/classes/installer/create.creds.php');
							$_createCreds	=	MySQLCredentials::Create($_POST);
						}
						
						return;
					}
					
					AutoloadFunction('check_empty');
					$arr_valid		=	(is_array($payload) && !empty($payload)); 
					$post_valid		=	(!empty($payload));
					$this->payload	=	($arr_valid)? $payload:$_POST;
					$nubquery		=	nQuery();
					
					// If a post request has been made
					if(!empty($this->payload)) {
						// If not logged in
						if(!is_loggedin()) {
							global $_incidental;
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
								$htaccess_conv	=	json_decode($htaccess_get[0]['content']);
								$htaccess		=	Safe::decode($htaccess_conv['htaccess']);
								$writeHTACCESS	=	SaveToDisk::Write(array("filename"=>'.htaccess',"payload"=>$htaccess,"write"=>'w'));
							}
						catch (Exception $e) {
								if(is_admin())
									echo printpre($e);
								
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
					
					$register	=	new RegisterSetting();
					// If found, continue
					if(!empty($get_new[0]['reset_password'])) {
							$pass			=	Safe::decode($this->payload['password']);
							$verified		=	$PasswordEngine->verify_password($pass,$get_new[0]['reset_password'])->valid;
							// If passwords match, assign data
							if($verified) {
								$this->reset	=	true;
								$this->data		=	$get_new[0];
								$register->UseData('pass_match',array("success"=>true))->SaveTo("incidentals");
								$register->UseData("reset_user",$this->payload["email"])->SaveTo("settings");
								// Pass an error message
								$_incidental['pass_match']	=	"Password match.";
							}
							else {
								$register->UseData('pass_match',array("success"=>false,"msg"=>"invalid"))->SaveTo("incidentals");
								// Pass an error message
								$_incidental['pass_match']	=	"Temporary password mis-match.";
							}
						}
					else {
						$register->UseData('pass_match',array("success"=>false,"msg"=>"error"))->SaveTo("incidentals");
						// Pass an error message
						$_incidental['pass_match']	=	"Invalid.";
					}
				}
			
			public	function Login()
				{
					AutoloadFunction("check_empty");

					$_setting['login']	=	(check_empty($_POST,'action','login'))? 1:0;
					$_setting['logout']	=	(check_empty($_REQUEST,'action','logout'))? 1:0;
					$_setting['remote']	=	(check_empty($_REQUEST,'action','login_remote'))? 1:0;
					$reload_page		=	false;
					if($_setting['remote'] == 1) {
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
						if(isset($_SESSION['token']['login'])) {							
							if(empty($_POST['token']['login'])) {
								global $_incidental;
								$_incidental['login']['bad_request']	=	'Bad request.';
								return;
							}
							
							AutoloadFunction("ValidateToken");
							if(!ValidateToken('login',$_POST['token']['login'])) {
								global $_incidental;
								$_incidental['login']['bad_request']	=	'Bad request.';
								return;
							}
							
							$_SESSION['token']['login']	=	NULL;
						}
						else
							return false;
					}	
					
					// Self-contained login
					if(array_sum($_setting) > 0) {
						// Destroy session
						if($_setting['logout']) {
								session_destroy();
								header("Location: ".str_replace("index.php","",$_SERVER['PHP_SELF']));
								exit;
							}
						else {	
							if(!empty(nApp::getPost('username'))) {
								AutoloadFunction("QuickWrite");
								// See if there is a login bypass file
								$_bypass	=	nApp::getBypass('login');
								$bfile		=	Safe::normalize_url(ROOT_DIR."/$_bypass");
								$_bypass	=	(is_file($bfile));
								// Write to log an attempt
								QuickWrite(array("data"=>"Token Pass: ".$_POST["username"].PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>CLIENT_DIR."/settings/error_log/","filename"=>"login.txt","mode"=>"c+"));
								// See if the page is an administration page
								$is_admin	=	(is_admin());
								// If it's not an administration page and bypass is valid
								if($_bypass && !$is_admin)
									include_once($bfile);
								// Process the login normally
								else
									processLogin::execute($_POST);
							}
						}	
					}

					if($reload_page) {
						header("Location: " . str_replace("index.php","",$_SERVER['PHP_SELF']));
						exit;
					}
				}
		}