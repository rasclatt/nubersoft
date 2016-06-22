<?php
	function silent_error()
		{
			register_use(__FUNCTION__);
			// Load incidental errors
			global $_incidental;
			// Load admin check
			AutoloadFunction('is_admin');	
			$prefs		=	(isset(NubeData::$settings->page_prefs))? NubeData::$settings->page_prefs: (object) array("page_live"=>false);
			// Check if page live is on.
			$offline	=	($prefs->page_live != 'on')? true:false;
			$bypass		=	(isset($settings['bypass']) && $settings['bypass'] == true)? true:false;
			$valid		=	(isset($prefs->page_valid))? $prefs->page_valid:false;
			$badpage	=	(isset($_incidental['404']))? true:false;
			
			$register	=	new RegisterSetting();
			$register->UseData('error404',$badpage)->SaveTo("settings");
			
			return (($offline == true && !is_admin()) || isset($_incidental['404']) || ($bypass == true) || $valid == false)? true : false;
		}