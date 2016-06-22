<?php
	function TokenMatch($settings = array('token_name'=>'email','request'=>'post'))
		{
			if(!isset($settings['token_name']) || (isset($settings['token_name']) && empty($settings['token_name'])))
				return false;
				
			$name	=	$settings['token_name'];
			
			if(isset($settings['request'])) {
					$settings['request']	=	strtolower($settings['request']);
					if($settings['request'] == 'request')
						$req	=	$_REQUEST;
					elseif($settings['request'] == 'get')
						$req	=	$_GET;
				}
			
			$req	=	(!isset($req))? $_POST : $req;
			
			if(!isset($req['token'][$name]))
				return false;
			else {
					// If there is an email token, check against session
					if(!isset($_SESSION['token'][$name]) || (isset($_SESSION['token'][$name])) && $_SESSION['token'][$name] != $req['token'][$name])
					return false;
				}
			
			return true;
		}
?>