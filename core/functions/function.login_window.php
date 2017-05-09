<?php
	function login_window($useLayout = false)
		{
			if(nApp::getPage('page_live') == 'off' || !nApp::getPage('page_live'))
				return false;

			$page_view		=	new ValidateLoginState();
			$login_valid	=	$page_view->Validate(nApp::getPage("session_status"))->login_required;
			ob_start();
			
			if(!empty($useLayout) && is_file($useLayout))
				$page_view->useLayout($useLayout,'d');

			$page_view->LoginPage();
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}