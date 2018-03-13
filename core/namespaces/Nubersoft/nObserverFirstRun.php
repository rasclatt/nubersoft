<?php
namespace Nubersoft;

class nObserverFirstRun extends \Nubersoft\nApp implements nObserver
{
	/*
	**	@description	Listening mode for user table
	*/
	public	function listen()
	{
		if(!defined('NBR_CORE_SETTINGS')) {
			$DS	=	DIRECTORY_SEPARATOR;
			include_once(__DIR__.$DS.'..'.$DS.'..'.$DS.'..'.$DS.'defines.php');
		}
		
		if($this->getPost('action') == 'nbr_install_database_credentials') {
			$this->setErrorMode(1);
			$this->installDatabaseCredentials();
			$this->installDefaultTables();
		}
		else {
			if($this->settingsManager()->isLiveMode())
				return;
		}
		
		if($this->userCount() == 0) {
			if(!empty($this->getSession('usergroup')))
				return;
			
			if(!defined('NBR_SUPERUSER')) {
				define('NBR_SUPERUSER',1);
			}
			
			$settings	=	array(	'usergroup'=>NBR_SUPERUSER,
									'username'=>'guest',
									'first_name'=>'Guest',
									'last_name'=>'User'
								);

			$location	=	(!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : $this->siteUrl();
			
			if(!empty($this->getTables()) && in_array('main_menus',$this->toArray($this->getTables()))) {
				$location	=	$this->adminPageExistsCreate();
			}
			
			if(empty($location))
				throw new nException("An error occurred setting up the database.");
			
			$this->getHelper('UserEngine')->loginUser($settings);
			$this->getHelper('nRouter')->addRedirect($location);
		}
	}
	
	public	function installDefaultTables()
	{
		$Query	=	$this->nQuery()->getConnection();
		require(NBR_SETTINGS.DS.'firstrun'.DS.'sql'.DS.'tables.php');
		
		foreach($data as $create) {
			try {
				$Query->query($create);
			}
			catch(\PDOException $e) {
				die(print_r($e->getMessage(),1));
			}
		}
		unset($data);
		require(NBR_SETTINGS.DS.'firstrun'.DS.'sql'.DS.'alters.php');
		foreach($alter as $create) {
			try {
				$Query->query($create);
			}
			catch(\PDOException $e) {
				die(print_r($e->getMessage(),1));
			}
		}
		require(NBR_SETTINGS.DS.'firstrun'.DS.'sql'.DS.'rows.php');
		foreach($data as $create) {
			try {
				$Query->query($create);
			}
			catch(\PDOException $e) {
				die(print_r([$e->getMessage(),$data],1));
			}
		}
	}
	
	public	function adminPageExistsCreate()
	{
		$count	=	$this->nQuery()
			->query("select COUNT(*) as count from `main_menus`")
			->getResults(true);

		if($count['count'] <= 1) {

			$count	=	$this->nQuery()
				->query("select COUNT(*) as count from `main_menus` where `is_admin` = '1'")
				->getResults(true);

			if($count['count'] == 0) {
				$this->getHelper('CoreMySQL')->installRows('main_menus');
				$admintools	=	$this->nQuery()->query("select `full_path` from main_menus where `is_admin` = 1")->getResults(true);

				if($admintools == 0)
					throw new \Exception('Could not create admin page');
				else
					return $this->siteUrl($admintools['full_path']);
			}
		}
		
		return false;
	}
	
	public	function installDatabaseCredentials($array = false)
	{
		if(empty($array))
			$array	=	$this->toArray($this->getPost('database'));

		if(empty($array))
			return false;

		$creds	=	$this->getHelper('FetchCreds')->createFile($array);
		if(is_file($file = NBR_CLIENT_DIR.DS.'settings'.DS))
			$this->getHelper('nRouter')->addRedirect($this->siteUrl());
	}
}