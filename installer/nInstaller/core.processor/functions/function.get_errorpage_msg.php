<?php
	function get_errorpage_msg($settings = false)
		{
			register_use(__FUNCTION__);
			
		//	echo printpre(NubeData::$settings);
			
			AutoloadFunction('check_empty');
			$message	=	(!empty($settings['message']))? $settings['message']:false;
			$is_live	=	(!empty(NubeData::$settings->preferences->site->content->site_live->value))? NubeData::$settings->preferences->site->content->site_live->value : false;
			
			if(!$message)
				echo (!empty($is_live))? Safe::decode($is_live) : "Site Updating";
			else
				echo Safe::decode($message);
		}