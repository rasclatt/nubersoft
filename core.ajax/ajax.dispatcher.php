<?php
include(__DIR__.'/../config.php');
if(!empty($_POST['action'])) {
	AutoloadFunction("is_ajax_request");
	
	if(!is_ajax_request())
		die(json_encode(array("error"=>"Must be an ajax request.")));

	if(isset(nApp::getPost()->deliver->formData)) {
		$dbWriter	=	new DBWriter();
		$formStr	=	Safe::decode(nApp::getPost()->deliver->formData);
		parse_str($formStr,$formData);
		nApp::getPost()->deliver->formData	=	$formData;
		$allow	=	\nApp::nToken()->resetTokenOnMatch($formData,'nProcessor','ajax',mt_rand(1000,9999));
		
		if($allow) {
			if(!empty($formData['requestTable']))
				$dbWriter->useTable($formData['requestTable']);
			
			$dbWriter->execute($formData);
		}
	}

	switch($_POST['action']) {
		case ('iforgot'):
			if(!is_admin())
				die("Not admin");
				
			include(NBR_AJAX_DIR.'/send.password.php');
			exit;
		case ('get_htaccess') :
			if(!is_admin())
				die("Not admin");
				
			include(NBR_AJAX_DIR.'/get.htaccess.php');
			exit;
		case ('autoset') :
			$function	=	(!empty(nApp::getPost('use')))? nApp::getPost('use') : 'unknown_driver';
			$plugin		=	(!empty(nApp::getPost('plugin')))? nApp::getPost('plugin') : false;
			$doAction	=	false;
			
			if(!empty(nApp::getPost('vars'))) {
				if(!empty(nApp::getPost('vars')->autorun))
					$doAction	=	true;
			}
			
			$uFile[]	=	NBR_ROOT_DIR.'/core.ajax/functions/function.'.$function.'.php';
			$uFile[]	=	NBR_CLIENT_DIR.'/core.ajax/functions/function.'.$function.'.php';
			$uFile[]	=	NBR_CLIENT_DIR.'/functions/function.'.$function.'.php';
			$uFile[]	=	NBR_CLIENT_DIR.'/plugins/'.$plugin.'/functions/function.'.$function.'.php';
			
			for($i = 0; $i < 4; $i++) {
				if(is_file($iFile = $uFile[$i])) {
					if($i == 0 && is_admin()) {
						include($iFile);
						
						if($doAction)
							$function();

						exit;
					}
					
					include($iFile);
					
					if($doAction)
						$function();
					exit;
				}
			}
		default:
			$settings	=	array(
								'listen_for'=>'action',
								'organize'=>'name',
								'dir'=>NBR_CLIENT_DIR
							);

			\nApp::nAutomator()->makeListener($settings);
	}
}