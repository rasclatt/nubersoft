<?php
namespace Nubersoft;

class nObserverFirstRun extends \Nubersoft\nApp implements nObserver
	{
		/*
		**	@description	Listening mode for user table
		*/
		public	function listen()
			{
				if($this->settingsManager()->isLiveMode())
					return;
				
				if($this->userCount() == 0) {
					if(!empty($this->getSession('usergroup')))
						return;
						
					$settings	=	array(	'usergroup'=>NBR_SUPERUSER,
											'username'=>'guest',
											'first_name'=>'Guest',
											'last_name'=>'User'
										);
					
					$location	=	(!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : $this->siteUrl();
					
					if(!empty($this->getTables()) && in_array('main_menus',$this->toArray($this->getTables()))) {
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
									$location	=	$this->siteUrl($admintools['full_path']);
							}
						}
					}
					
					$this->getHelper('UserEngine')->loginUser($settings);
					$this->getHelper('nRouter')->addRedirect($location);
				}
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