<?php
	class ValidateSession
		{
			public	static function Check($expireTime = false)
				{
					register_use(__METHOD__);
					$refer	=	(!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']:"http://".$_SERVER['HTTP_HOST'];
					if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $expireTime)){
						exit;
							session_unset();     // unset $_SESSION variable for the runtime 
							session_destroy();   // destroy session data in storage
							NubeData::$settings->user->admin = NubeData::$settings->user->usergroup = NubeData::$settings->user->loggedin = false;
							header("Location: ".$refer);
							exit;
						}
						
					if(isset($_SESSION['username']))
						$_SESSION['LAST_ACTIVITY']	=	time(); // update last activity time stamp
				}
		}