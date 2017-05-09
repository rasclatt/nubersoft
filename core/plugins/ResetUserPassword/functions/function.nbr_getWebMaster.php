<?php
function nbr_getWebMaster($msg = false)
	{
		if(empty($msg))
			$msg	=	'You must define a "webmaster" value in your registry.xml in the '.\Nubersoft\nApp::call('Safe')->encodeSingle('<').'ondefine'.\Nubersoft\nApp::call('Safe')->encodeSingle('>');
			
		if(!defined('WEBMASTER')) {
			throw new \Exception($msg);
			\Nubersoft\nApp::call()->saveIncidental('preload',array('send_email_notice'=>'error','msg'=>$msg));
			return false;
		}
		
		return true;
	}