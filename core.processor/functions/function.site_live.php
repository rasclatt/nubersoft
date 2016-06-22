<?php
	function site_live()
		{
			
			AutoloadFunction('get_site_prefs,silent_error');
			$values		=	nApp::getSitePrefs('site');
			$toggled	=	(isset($values->content->site_live->toggle) && $values->content->site_live->toggle == 'on');
			// Register 404
			silent_error();
			return	$toggled;
		}