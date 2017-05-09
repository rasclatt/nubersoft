<?php
	function fetch_admin_link($return = false)
		{
			if(!nApp::siteValid())
				return;
				
			if(!empty(nApp::getAdminPage()) && isset(nApp::getAdminPage()->full_path)) {
				return (!$return)? nApp::getAdminPage()->full_path : Safe::to_array(nApp::getAdminPage());
			}
		}