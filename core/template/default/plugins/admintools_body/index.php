<?php
if(!empty($this->getGet('admintool_plugin')))
	echo $this->useTemplatePlugin('plugin_'.$this->getGet('admintool_plugin'));
else {
	$body	=	($this->isAdmin())? 'admintool' : 'admin_login';
	echo $this->useTemplatePlugin($body);
}