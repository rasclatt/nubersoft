<?php
namespace nPlugins\Nubersoft;

class CoreLogin extends \Nubersoft\HeadProcessor
	{
		private	$actionName;
		
		public	function addAction($name)
			{
				$this->actionName	=	$name;
				return $this;
			}
		
		public	function login($skip = false,$response = false)
			{
				# Fetch the acion name from post
				$this->actionName	=	(!empty($this->actionName))? $this->actionName : 'nbr_admin_login';
				# If there is an error (to figure out later) in the preference (puts this action first for some reason before session is set)
				if(!$this->getHelper('nSessioner')->sessionStarted()) {
					# If an action is specified
					if(!empty($this->actionName)) {
						# Get the redirect (loaded before the parsing of the server array)
						$redirect	=	(empty($this->getDataNode('_SERVER')))? $this->safe()->encodeSingle($_SERVER['SCRIPT_URI']) : $this->getDataNode('_SERVER')->SCRIPT_URI;
						# Delete all the content from the prefs folder
						$this->getHelper('nFileHandler')->deleteContents($this->getCacheFolder().DS.'prefs');
						# Save a program error
						$this->toAlert('Program error','login');
						# Redirect back
						$this->getHelper('nRouter')->addRedirect($redirect);
					}
				}
				
				$allowed		=	false;
				$reload_page	=	false;
				$redirect		=	(isset($this->getDataNode('_SERVER')->PHP_SELF))? $this->nRouter->stripIndex($this->getDataNode('_SERVER')->PHP_SELF) : $this->adminUrl();
				# Check token
				if($this->getPost('action') == $this->actionName && $this->getPageURI('is_admin') == 1) {
					$errMsg['missing']	=	'Login token missing. Reload the page to try again.';
					$errMsg['required']	=	'Token required to login.';
					# If token exists for login
					$tokenExists	=	$this->getHelper('nToken')->tokenExists('login');
					# If the token exists, valiate it
					if($tokenExists) {
						# If the token is empty, record and return							
						if(empty($this->getPost('token')->login)) {
							$this->saveIncidental('login',array('bad_request'=>$errMsg['missing']),true);
							return;
						}
						# Check if token is set and matches, then clear it
						$token	=	$this->getHelper('nToken')->tokenMatch('login');
						# If the token is set, but does not match, just return
						if(!$token) {
							$this->saveIncidental('login',array('bad_request'=>$errMsg['missing']),true);
							return;
						}
						else
							$allowed	=	true;
					}
					else {
						$this->saveIncidental('login',array('bad_request'=>$errMsg['required']),true);
						return false;
					}
				}	
				
				# Self-contained login
				if($allowed) {
					# See if the page is an administration page
					$aPage		=	
					$is_admin	=	($this->getPageURI('is_admin') == 1);
					$username	=	$this->getPost('username');
					if(!empty($username)) {
						# No bypass allowed
						$_bypass	=	false;
						$lMsg		=	PHP_EOL.date('Y-m-d H:i:s')." TOKEN: [OK] USERNAME: [".$this->getPost("username")."] LINE: [".__LINE__."] CLASS: [".__CLASS__.']';
						$fName		=	array(
											"path"=>NBR_CLIENT_DIR.DS."settings".DS.'reporting'.DS."errorlogs".DS,
											"filename"=>"login.txt"
										);
						
						# Check if the user is valid
						$user		=	$this->getUserInfo($this->getPost('username'));
						# If user invalid, return
						if(!$user) {
							$incOpts	=	array('error'=>'invalid username/password');
							# Save incidental
							$this->saveIncidental('login',$incOpts,true);
							$this->saveToLogFile($fName,$lMsg.': INVALID LOGIN',array('logging'),array('type'=>'c+'));
							return false;
						}						
						# If the referring page is not admin page
						if(!$aPage) {
							# If admin user valid
							if($this->isAdmin($user->usergroup)) {
								# Check if allow from any-page-admin-login is set
								$openAllow	=	true;
								# See if there is a define to keep it off
								if(defined("OPEN_ADMIN")) {
									$openAllow	=	OPEN_ADMIN;
								}
								# If not allowed to log in except for admin page stop
								if(!$openAllow) {
									# Browser-facing message
									$getCustMsg	=	$this->getMatchedArray(array('messaging','login','client','fail','admin'));
									# Get custom messaging
									$nMsg	=	(!empty($getCustMsg['admin'][0]))? $this->getHelper('nAutomator',$this)->matchFunction($getCustMsg['admin'][0]) : 'logging into admin area must be done through admin page.';
									# Set error into data array
									$msg	=	array('error'=>$nMsg);
									# Save incidental for browser alert
									$this->saveIncidental('login',$msg,true);
									$this->setSession('login',array('msg'=>$nMsg));
									# Save to log
									$fName['filename']	=	'login_'.date('YmdHis').time().'.log';
									$this->saveToLogFile($fName,'ERROR: Admin user login on non-admin page. You must log in using your admin page found at '.$this->getFunction('site_url').$this->getAdminPage('full_path').' or switch your constant in your registry to "true": <open_admin>true</open_admin>',array('logging','exceptions'));
									# Stop action
									return false;
								}
							}
						}
						# Continue logging in
						$this->getHelper('processLogin')->execute($_POST);
						$success	=	$this->getIncidental('login')->{0};
					}
				}
				
				if(!$skip) {
					if(isset($success)) {
						# If there is a jumppage, execute it
						if($this->getPost('jumppage')) {
							$path	=	$this->safe()->decOpenSSL($this->getRequest('jumppage'));
							$this->processJumpPage($path);
						}
						# If there is an auto-forward after loging on
						elseif(!empty($this->getPageURI('auto_fwd_post'))){
							# Get path
							$path	=	$this->getPageURI('auto_fwd');
							# If there is a path to auto-forward to
							if(!empty($path)) {
								if($path !== 'NULL') {
									# If there is no external path, just tack on the site url
									if(strpos($path,'http') !== true)
										$path	=	$this->localeUrl($path);
									# Route to a new page
									$this->nRouter->addRedirect($path);
								}
							}
						}
						
						# Redirect back to self
						$this->nRouter->addRedirect($redirect);
					}
				}
			}
		/*
		**	@description	Logs in with a code from the post and the session matched
		*/
		public	function loginWithCode()
			{
				$action			=	(!empty($this->getPost('action')))? $this->getPost('action') : 'nbr_code_login';
				# Fetch the session array
				$codeArr		=	$this->toArray($this->getSession('login_temp_code'));
				# Fetch the code from the post
				$code			=	$this->getPost('code');
				$err['invalid']	=	'Invalid code.';
				$err['match']	=	'Invalid code match.';
				# If the session code is non-existant, stop
				if(empty($codeArr['code'])) {
					$this->toAlert($err['invalid'],$action);
					return;
				}
				# If there is no posted code, stop
				if(empty($code)) {
					$this->toAlert($err['invalid'],$action);
					return;
				}
				# If the codes don't match, stop
				if($codeArr['code'] != $code) {
					$this->toAlert($err['match'],$action);
					return;
				}
				# Clear the stored session
				$SESSION	=	$this->getHelper('nSessioner');
				$SESSION->destroy('login_temp_code');
				$this->setSession('temp_access',true,true);
				$this->getHelper('UserEngine')->loginUser($codeArr['user']);
				$this->getHelper('nRouter')->addRedirect($this->adminURL());
			}
		
		public	function isSiteUser()
			{
				$POST	=	$this->toArray($this->getPost('data'));
				if(empty($POST['username'])) {
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>'Username can not be empty.'));
					else
						return false;
				}
					
				$result	=	$this->Users()->getUser($POST['username']);
				
				if($result != 0)
					$this->loginByRequest();
			}
		/*
		**	@description	Takes a signup form from ajax and coverts it to and instruction object
		*/
		public	function convertFormDataToPost()
			{
				$POST		=	(!empty($this->getPost('formData')))? $this->getPost('formData') : $this->getPost('data');
				$FORM		=	(is_string($POST))? $this->getHelper('nForm')->deliverToArray($POST) : $POST;
				$fields		=	array_keys($this->organizeByKey($this->nQuery()->query('describe users')->getResults(),'Field',array('multi'=>true)));
				if(is_object($FORM))
					$FORM	=	$this->toArray($FORM);
					
				$nProcess	=	(isset($FORM['token']['nProcessor']))? $FORM['token']['nProcessor'] : $FORM['token'];
				
				foreach($FORM as $key => $value) {
					if(!in_array($key,$fields))
						unset($FORM[$key]);
				}
				
				$this->getHelper('NubeData')->destroy('settings','_POST');
				$this->saveSetting('_POST',array('data'=>array_merge(array('token'=>$nProcess,'unique_id'=>$this->fetchUniqueId()),$FORM)));
				return $this;
			}
		
		public	function hasTempLogin($username)
			{
				$user	=	$this->getHelper('UserEngine')->getUser($username);
				return (!empty($user['reset_password']))? $user['reset_password'] : false;
			}
		/*
		**	@description	Ajax-based login system to both check and add user into the system
		*/
		public	function loginByRequest()
			{
				$SESSION	=	$this->toArray($this->getSession('token'));
				$POST		=	$this->trimAll($this->toArray($this->getPost('data')));
				$invalid	=	false;
				
				if(empty($SESSION['nProcessor']['page']))
					$invalid	=	true;
				elseif(empty($POST['token']))
					$invalid	=	true;
				elseif($POST['token'] != $SESSION['nProcessor']['page'])
					$invalid	=	true;
				
				if($invalid) {
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>'Not a valid request.'));
					else
						return false;
				}
				
				$User		=	$this->getHelper('User');
				$username	=	$POST['username'];
				$password	=	$POST['password'];
				# First check if this user is an admin user
				$isAdmin	=	$this->getHelper('UserEngine')->isAdmin(trim($username));
				if(defined('OPEN_ADMIN')) {
					if(!$this->getBoolVal(OPEN_ADMIN)) {
						# If admin, send invalid. Required to login to the admin page
						if($isAdmin) {
							$msg	=	'Invalid login. Status Invalid.';
							if($this->isAjaxRequest())
								$this->ajaxResponse(array(
									'alert'=>$msg,
									'html'=>array(''),
									'sendto'=>array('.nbr_action_loader')
								)
							);
							
							$this->toAlert($msg,'login');
						}
					}
				}
				$valid	=	$User->login($username,$password);
				$err	=	$this->toArray($this->getIncidental('login_error'));
				
				# Attempt to log in using temporary password
				if(is_array($err) && in_array('creds',$err)) {
					$tPass	=	$this->hasTempLogin($username);
					if($tPass) {
						$User->login($username,$password,'reset_password');
					}
				}
				
				# If user is in system but login wrong
				if(is_array($err) && in_array('creds',$err) && !$this->isLoggedIn()) {
					$msg	=	'Invalid password.';
					# Immediate ajax response
					if($this->isAjaxRequest())
						$this->ajaxResponse(array(
							'alert'=>$msg,
							'html'=>array(''),
							'sendto'=>array('.nbr_action_loader')
						)
					);
					# Save conventional error
					$this->toAlert($msg,'login');
				}
				if(is_array($err) && in_array('none',$err)) {
					if(empty($username))
						$invalid	=	'Username can not be empty.';
					elseif(!filter_var($username,FILTER_VALIDATE_EMAIL))
						$invalid	=	'Username is not a valid email address.';
					elseif(empty($password))
						$invalid	=	'Password can not be empty.';
					elseif(strlen($password) < 8)
						$invalid	=	'Password has to be at least 8 characters.';
					
					if($invalid) {
						if($this->isAjaxRequest())
							$this->ajaxResponse(array('alert'=>$invalid,'html'=>array(''),'sendto'=>array('.nbr_action_loader')));
						else
							return false;
					}
					
					$userTable	=	$this->toArray($this->getColumns('users'));
					# Create variables for saving
					$POST['first_name']	=	(!empty($POST['first_name']))? $POST['first_name'] : $username;
					$POST['last_name']	=	(!empty($POST['last_name']))? $POST['last_name'] : 'Guest';
					$POST['password']	=	$this->getHelper('PasswordVerify')->hash($password)->getHash();
					$POST['unique_id']	=	$this->fetchUniqueId();
					$POST['usergroup']	=	(!empty($POST['usergroup']))? $POST['usergroup'] : 'NBR_WEB';
					$POST['email']		=	(!empty($POST['email']) && filter_var($POST['email'],FILTER_VALIDATE_EMAIL))? $POST['email'] : $POST['username'];
					$POST['country']	=	(!empty($POST['country']))? trim($POST['country']) : 'USA';
					
					if(empty($POST['date_created']))
						$POST['date_created']	=	date('Y-m-d H:i:s');
					
					$i = 0;
					foreach($userTable as $col) {
						if(isset($POST[$col])) {
							$bind[$i]	=	$POST[$col];
							$cols[]		=	$col;
							$vals[]		=	":{$i}";
							$i++;
						}
					}
					# Insert into the user into the database
					$this->nQuery()->query("INSERT INTO `users` (".'`'.implode('`, `',$cols).'`'.") VALUES (".implode(',',$vals).")",$bind);
					# Get the user to make sure they have been added
					$results	=	$User->getUser($username);
					# If they have not been added alert to it
					if($results == 0) {
						if($this->isAjaxRequest())
							$this->ajaxResponse(array('alert'=>'An error occurred signing you up.','error'=>$this->getError(),'html'=>array(''),'sendto'=>array('.nbr_action_loader')));
						else
							return false;
					}
					# Log in the user
					return $User->login($username,$password);
				}
				else {
					if(is_array($err) && in_array('off',$err)) {
						if($this->isAjaxRequest())
							$this->ajaxResponse(array(
								'alert'=>'User account has been suspended.',
								'html'=>array(' '),
								'sendto'=>array('.nbr_action_loader')
							));
						else
							return false;
					}
					
					return $User->login($username,$password);
				}
			}
	}