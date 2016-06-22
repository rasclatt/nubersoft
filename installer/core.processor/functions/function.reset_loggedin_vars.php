<?php
	function reset_loggedin_vars()
		{
			// Update the nubersoft user credentials
			RegistryEngine::saveSetting('user',array('loggedin'=>is_loggedin()));
			RegistryEngine::saveSetting('user',array('usergroup'=>(is_loggedin())? (int) $_SESSION['usergroup']: false));
			RegistryEngine::saveSetting('user',array('admin'=>is_admin()));
			RegistryEngine::saveSetting('user',array('admission'=>(is_loggedin() && !is_admin())));
		}