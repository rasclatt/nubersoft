<?php
namespace Nubersoft;

class User extends \Nubersoft\UserEngine
	{
		public	function login($username,$password,$col = 'password')
			{
				$nApp		=	nApp::call();
				$username	=	trim($username);
				$password	=	trim($password);
				
				if(empty($username) || empty($password))
					return false;
					
				$result	=	$this->getUser($username);
				
				if($result == 0) {
					$nApp->saveIncidental('login',false);
					$nApp->saveIncidental('login_error','none',true);
					return false;
				}
				elseif($result['user_status'] != 'on') {
					$nApp->saveIncidental('login',false);
					$nApp->saveIncidental('login_error','off',true);
					return false;
				}
				
				# Check password_hash algo
				$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
				$validate		=	$PasswordEngine	->setUser($username)
													->setCheckColumn($col)
													->verifyPassword($password,$result[$col])
													->valid;

				$nApp->saveIncidental('login',$validate,true);
				
				if($validate) {
					$result['usergroup']	=	$nApp->convertUserGroup($result['usergroup']);
					$SESSION	=	$this->toArray($nApp->getSession());
					$SESSION	=	(is_array($SESSION))? array_merge($result,$SESSION) : $result;
					$nApp->getHelper('nSessioner')->toEmpty()->makeSession($SESSION);
				}
				else
					$nApp->saveIncidental('login_error','creds',true);
				
				# Update the nubersoft user credentials
				$nApp->saveSetting('user',array(
					'loggedin'=>$nApp->isLoggedIn(),
					'usergroup'=>$nApp->getSession('usergroup'),
					'admin'=>$nApp->isAdmin(),
					'admission'=>($nApp->isLoggedIn() && !$nApp->isAdmin())
				));
				
				return $nApp->isLoggedIn();
			}
		/*
		**	@description	Check if user exists
		*/
		public	function userExists($username)
			{
				$nApp		=	nApp::call();
				$nQuery		=	$nApp->nQuery();
				$username	=	trim($username);
				$getUser	=	$nQuery->query("select COUNT(*) as count from `users` WHERE `username` = :0",array($username))->getResults(true);
				
				return ($getUser['count'] > 0);
			}
		
		public	function create($username,$password,$data = false)
			{
				$nApp		=	nApp::call();
				$nQuery		=	$nApp->nQuery();
				$username	=	trim($username);
				$exists		=	$this->userExists($username);
				
				if($exists)
					return true;
				
				$hash	=	$nApp->getHelper('PasswordVerify')->hash($password)->getHash();
				$base	=	array('username'=>$username,'password'=>$hash);
				$data	=	(is_array($data))? array_merge($base,$data) : $base;
				
				if(empty($data['first_name']))
					$data['first_name']	=	'Guest';
					
				if(empty($data['last_name']))
					$data['last_name']	=	'User';
				
				if(!isset($data['page_live']))
					$data['user_status']	=	'on';
					
				if(!isset($data['usergroup']))
					$data['usergroup']	=	'NBR_WEB';
				
				if(empty($data['date_created']))
					$data['date_created']	=	date('Y-m-d H:i:s');
				
				$cols	=	array_keys($data);
				foreach($cols as $val) {
					$bKey		=	":{$val}";
					$values[]	=	$bKey;
					$columns[]	=	"`{$val}`";
				}
				
				$data	=	$nApp->getHelper('Submits')->sanitizeData($data);
				
				$nQuery->query("INSERT INTO `users` (".implode(', ',$columns).") VALUES(".implode(", ",$values).")",$data);
				return $this->userExists($username);
			}
	}