<?php
namespace Nubersoft;

class nObserverFirstRun implements nObserver
	{
		public	static	function listen()
			{
				if(\nApp::userCount() == 0) {
					if(!empty($_SESSION['usergroup']))
						return;
						
					$settings	=	array(	'usergroup'=>NBR_SUPERUSER,
											'username'=>'guest',
											'first_name'=>'Guest',
											'last_name'=>'User'
										);
					\nApp::nFunc()->autoload('site_url',NBR_FUNCTIONS);
					$location	=	(!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : site_url();
					\nApp::UserEngine()->loginUser($settings);
					header('Location: '.$location);
					exit;
				}
			}
	}