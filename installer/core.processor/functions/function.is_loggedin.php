<?php

function is_loggedin($settings = false)
	{
		$username	=	(!empty($settings['username']))? $settings['username'] : false;
		$usergroup	=	(!empty($settings['usergroup']))? $settings['usergroup'] : false;
		$loggedin	=	(!empty(nApp::getUser()->loggedin))? true : (!empty($_SESSION['username']));
		
		// If a username is not set, return false
		if(!$loggedin)
			return false;
					
		// If a username is specified
		if($username) {
				// If the userame is set and matching is part of a list of users
				if(is_array($username))
					return (in_array($_SESSION['username'],$username));
				else
					return ($username == $_SESSION['username']);
			}
		elseif($usergroup) {
				// If the userame is set and matching is part of a list of users
				if(is_array($usergroup))
					return (in_array($_SESSION['usergroup'],$usergroup));
				else
					return ($usergroup == $_SESSION['usergroup']);
			}
		else
			return true;
	}