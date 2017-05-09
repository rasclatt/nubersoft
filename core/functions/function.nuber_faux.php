<?php
	function nuber_faux($extra = false)
		{
			//AutoloadFunction('get_bypass,organize,is_admin,check_empty,fetch_table_name,fetch_table_id,is_loggedin');
			
			$settings['engine']['table']		=	(!empty($_REQUEST['requestTable']))? preg_replace("/[^0-9a-zA-Z\.\_\-]/","",$_REQUEST['requestTable']) : 'users';
			$settings['engine']['action']		=	nApp::getPost('action');
			$settings['engine']['command']		=	nApp::getPost('command');
			$settings['engine']['def_table']	=	(nApp::getGet('requestTable'))? false : true;
			$settings['engine']['htaccess']		=	(is_file(NBR_ROOT_DIR.'/.htaccess'));
			$settings['user']['loggedin']		=	(is_loggedin());
			$settings['user']['usergroup']		=	(is_loggedin())? (int) $_SESSION['usergroup']: false;
			$settings['user']['admin']			=	is_admin();
			$settings['user']['admission']		=	(is_loggedin() && !is_admin());
			
			if(is_array($extra)) {
				foreach($extra as $key => $value) {
					if(isset($settings[$key])) {
						$settings[$key]	=	array_merge($settings[$key],$extra[$key]);
					}
					else
						$settings[$key]	=	$extra[$key];
				}
			}
			foreach($settings as $keys => $values) {
				nApp::saveSetting($keys,$values);
			}
		}