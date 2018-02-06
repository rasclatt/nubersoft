<?php
namespace Nubersoft;

class	UserEngine extends \Nubersoft\nFunctions
{
	protected	$user	=	array();
	protected	$nApp;

	public	function __construct()
	{
		$this->nApp	=	nApp::call();

		return parent::__construct();
	}

	public	function loginUser($array = array())
	{
		$username	=	(!empty($array['username']))? $array['username'] : 'guest'.mt_rand().date('YmdHis');
		$usergroup	=	(!empty($array['usergroup']))? $this->nApp->convertUserGroup($array['usergroup']) : NBR_WEB;
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
		$nSession				=	$this->nApp->getHelper('nSessioner');
		// Make the array a session
		$nSession->makeSession($settings);
	}

	public	function logInUserWithCreds($username = false,$password = false)
	{
		if(empty($username) || empty($password))
			return false;

		$result	=	$this->getUser($username);

		if($result == 0)
			return false;

		// Check password_hash algo
		$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
		$validate		=	$PasswordEngine	->setUser($username)
											->verifyPassword($password,$result['password'])
											->valid;
		// If false, try with bcrypt
		if(!$validate) {
			$PasswordEngine	=	PasswordGenerator::Engine(PasswordGenerator::BCRYPT);
			$validate		=	$PasswordEngine	->setUser($username)
												->verifyPassword($password,$result['password'])
												->valid;
		}
	}

	public	function getUser($username,$all = true)
	{
		if(empty($username))
			return 0;

		return $this->nApp->nQuery()
				->query("select * from `users` where `username` = :0".((!$all)? " AND `user_status` = 'on'":''),array($username))
				->getResults(true);
	}
	/**
	*	@description	Checks if the input user group is allowed to view content by checking session usergroup
	*/
	public	function allowIf($usergroup = 3)
	{
		if(is_string($usergroup))
			$usergroup	=	$this->nApp->convertUserGroup($usergroup);
		
		if(!is_numeric($usergroup))
			return false;

		return ($this->getUser($usergroup) <= $usergroup);
	}
	/**
	*	@description	Checks if the current 
	*/
	public	function isAllowed($usergroup)
	{
		if(empty($usergroup))
			return true;
 
		$usergroup	=	$this->getUsergroupFromValue($usergroup);
		
		if(!$usergroup)
			return false;
		
		return ($this->nApp->getSession('usergroup') <= $usergroup);
	}
	
	protected	function getUsergroupFromValue($usergroup)
	{
		if(is_string($usergroup))
			$usergroup	=	$this->nApp->convertUserGroup($usergroup);
		
		if(!is_numeric($usergroup))
			return false;
		
		return $usergroup;
	}
	
	public	function isAdmin($username = false)
	{
		if(!empty($username)) {
			$user	=	$this->getUser($username);
			if($user == 0)
				return false;

			$usergroup	=	$this->nApp->convertUserGroup($user['usergroup']);
		}
		else
			$usergroup	=	(!empty($this->nApp->getDataNode('_SESSION')->usergroup))? $this->nApp->getDataNode('_SESSION')->usergroup : false;

		if(!is_numeric($usergroup)) {
			if(is_string($usergroup))
				$usergroup	=	$this->nApp->convertUserGroup($usergroup);

			if(!is_numeric($usergroup))
				return false;
		}

		return $this->groupIsAdmin($usergroup);
	}

	public	function groupIsAdmin($usergroup)
	{
		if(!defined('NBR_ADMIN')) {
			if(is_file($inc = NBR_CLIENT_SETTINGS.DS.'usergroups.php'))
				include_once($inc);
			else
				include_once(NBR_SETTINGS.DS.'usergroups.php');
		}

		return	($usergroup <= NBR_ADMIN);
	}

	public	function isLoggedin($username = false)
	{
		if(!empty($username))
			return ($this->nApp->getSession('username') == $username);

		return (!empty($this->nApp->getSession('username')));
	}

	public	function isLoggedInNotAdmin($username = false)
	{
		return ($this->isLoggedin($username) && !$this->isAdmin($username));
	}

	public	function hasAdminAccounts()
	{
		$sql	=	"SELECT
						COUNT(*) as count
						FROM `users`
						WHERE `usergroup` = 'NBR_SUPERUSER'
							OR
						`usergroup` = 'NBR_ADMIN'";

		$nQuery	=	$this->nApp->nQuery();
		$count	=	$nQuery
			->query($sql)
			->getResults(true);

		return ($count['count'] >= 1);
	}

	public	function getUserData()
	{
		return $this->user;
	}

	public	function isActive($username = false)
	{
		if(empty($username)) {
			if(!isset($this->user['active']))
				trigger_error('You have not set a username to check against or it has been reset.',E_USER_NOTICE);
			return (!empty($this->user['active']));
		}
		else {
			if(isset($this->user['username']) && $this->user['username'] == $username)
				return $this->user['active'];
		}

		$sql		=	"SELECT COUNT(*) as count FROM users WHERE username = :0 and user_status = 'on'";
		$query		=	$this->nApp->nQuery()->query($sql,array($username))->getResults(true);
		$this->user	=	[
			'username' => $username,
			'active' => ($query['count'] > 0)
		];

		return $this->user['active'];
	}
}