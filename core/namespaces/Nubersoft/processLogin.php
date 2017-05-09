<?php
namespace Nubersoft;

class processLogin extends \Nubersoft\nApp
	{
		public	static	$request_array,
						$password_encode,
						$bypass_login,
						$bypass_file;
		
		public	function execute()
			{
				self::$request_array	=	$this->toArray($this->getPost());
				self::$password_encode	=	'sha512';
				# Remote login check
				$_remLogin				=	($this->getEngine('action') == 'login_remote');
				if((isset(self::$request_array['login']) || $_remLogin) && !empty(self::$request_array['username']) && !empty(self::$request_array['password'])) {
					# Only process login if user is not already logged in.
					if(!isset($_SESSION['usergroup'])) {
						$username	=	self::$request_array['username'];
						$password	=	self::$request_array['password'];
						# If the user is request to login in remotely
						if($_remLogin) {
							$_creds['username']	=	(!empty($_POST['username']))? $_POST['username']:0;
							$_creds['password']	=	(!empty($_POST['password']))? $_POST['password']:0;
							$_creds['apikey']	=	(!empty($_POST['apikey']))? $_POST['apikey']:0;
							$_creds['domain']	=	(!empty($_POST['domain']))? $_POST['domain']:0;
							if(!in_array('0',$_creds)) {
								# Login for guest user on first install
								$response	=	$this->fetchRemote($_creds);
								if(isset($response['login']) && $response['login'] == 1) {
									$_SESSION['usergroup']	=	0;
									$_SESSION['username']	=	$_creds['username'];
									$_SESSION['first_name']	=	'Guest';
									$_SESSION['last_name']	=	'User';
									$_success				=	true;
								}
							}
								
							if(!isset($_success)) {
								$this->saveIncidental('login',array('success'=>false,'error'=>'Username/Password/API','type'=>'mismatch'),true);
							}
						}
						# Else login locally
						# Verify username and password
						else{
							$success	=	Saline::verify($username,$password);
							$this->saveIncidental('login',$success,true);
						}
					}
				}
				
				$userset	=	(isset($_SESSION['username']))? $_SESSION['username'] : false;
				
				if($userset && !isset($this->getIncidental('login')->success))
					$this->saveIncidental('login',true,true);
				
				if(!$userset) {
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>'Invalid Username/Password'));
				}
					
				# Update the nubersoft user credentials
				$this->saveSetting('user',array(
					'loggedin'=>(!empty($userset)),
					'usergroup'=>((!empty($userset))? (int) $_SESSION['usergroup']: false),
					'admin'=>$this->isAdmin(),
					'admission'=>($this->getFunction('is_loggedin') && !$this->isAdmin())
				));
				# Write to log an attempt
				//die(printpre(nbr_fetch_error('login',__FILE__,__LINE__)));
				self::call('nLogger')->ErrorLogs_Login($this->renderError('login',__CLASS__,__LINE__));
			}

		public	function login($request_array)
			{
				self::$request_array		=	$request_array;
				self::$password_encode		=	(!empty(self::$password_encode))? self::$password_encode: 'sha512';

				# Saved settings for the mysql credentials
				if($this->checkBypassInclude()) {
					if(self::$bypass_login !== false) 
						$_bypassed	=	true;
				}

				if(isset($_bypassed))
					include(self::$bypass_file);
				else
					$this->execute(self::$request_array, self::$password_encode);
			}
		
		public	function checkBypassInclude()
			{
				$nuber	=	$this->nQuery();
				
				if($nuber) {
					//Check to see if the login is bypassed
					$bypass	=	$nuber	->select("content")
										->from("system_settings")
										->where(array("component"=>'bypass',"name"=>'login',"page_live"=>'on'))
										->getResults();
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
			
		protected function	fetchRemote($_creds = array())
			{
				$this->autoload("check_empty");
				if(!empty($_creds) && check_empty($_POST,'action','login_remote')) { 
					$_remote	=	'http://www.nubersoft.com/api/index.php?service=Fetch.Account&action=login_remote&username='.urlencode($_creds['username']).'&password='.urlencode($_creds['password']).'&apikey='.urlencode($_creds['apikey']).'&domain='.urlencode($_creds['domain']);
					
					$connect	=	self::getClass('cURL',$_remote);
					return $connect->response; 
				}
			}
		
		protected	function renderError($code = false,$file = false,$line = false)
			{
				$isAdmin	=	($this->isAdmin())? 'Y' : 'N';
				$isSuccess	=	(!empty($this->getDataNode('user')->loggedin))? 'Y' : 'N';
				$error['whitelist']	=	array(
					"content"=>"IDENTITY REFUSED. Whitelist".PHP_EOL."{$file} | {$line}".PHP_EOL,
					'die'=>'<h1>Error: 550</h1></p>Permission denied</p>',
					'headers'=>array('http/1.1 550 permission denied')
				);
				
				$error['login']	=	array(
					"content"=>PHP_EOL.date('Y-m-d H:i:s')." LOGIN: [".$this->getPost('username').'] SUCCESS: ['.$isSuccess."] ADMIN: [{$isAdmin}] LINE: [{$line}] CLASS: [{$file}]",
					"die"=>false,
					"headers"=>false,
					'match'=>array('logging','login')
				);
				
				$error['unknown']	=	array(
					"content"=>"UNKNOWN ERROR: [".$_SERVER['REMOTE_ADDR'].']'.PHP_EOL." FILE: [{$file}] LINE: [{$line}]".PHP_EOL,
					'die'=>false,
					'headers'=>false
				);
				
				
				return (isset($error[$code]))? $error[$code] : $error['unknown'];
			}
	}