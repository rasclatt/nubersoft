<?php
/*Title: get_timezone()*/
/*Description: This function will either retieve the current stored timezone or if not set, will fetch and save it from the system.*/
	function get_timezone($default = 'America/Los_Angeles')
		{
			
			AutoloadFunction('get_site_options');
			
			if(isset(NubeData::$settings->timezone) && !empty(NubeData::$settings->timezone))
				return NubeData::$settings->timezone;
			
			$prefs		=	get_site_options();
			$timezone	=	(isset($prefs->timezone) && !empty($prefs->timezone))? $prefs->timezone : $default;
			$settings	=	new RegisterSetting();
			$settings->UseData('timezone',$timezone)->SaveTo('settings',true);
			return	$timezone;
		}
?>