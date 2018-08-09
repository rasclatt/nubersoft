<?php
# Checks if temporary access has been set. If so, allow user to continue
if(!empty($this->getSession('temp_access')))
	return;
# Get the ip of user
$remote	= $this->getClientIp();
# If user not on whitelist
if(!$this->onWhiteList($remote)) {
	# Send forbidden header
	$this->getHelper('nRouter')
		->addHeader(array('HTTP/1.0 403 Forbidden'))
		->addHeaderCode(403);
	# Set default message
	$msg	=	'403 Permission Denied.';
	# If ajax, just send back response
	if($this->isAjaxRequest())
		$this->ajaxResponse(array('html'=>array('<h1>'.$msg.'</h1><script>alert(\''.$msg.'\')</script>'),'sendto'=>array('body')));
	else {
		$codeArr	=	$this->toArray($this->getSession('login_temp_code'));
		$sms		=	$this->getRegistry('sms_options');
		
		if(!empty($sms['sms_carrier'])) {
			if(!isset($sms['sms_carrier'][0]))
				$sms['sms_carrier']	=	array($sms['sms_carrier']);
			$this->saveSetting('plugin_data_settings_admindeny',['data'=>$sms,'msg'=>$msg,'codearr'=>$codeArr]);
			echo $this->setDefaultRenderTemplate(false,'head','admintools',true).
				$this->useTemplatePlugin('whitelist','view.php');
		}
		else
			echo $msg;
	}
		
	exit;
}
