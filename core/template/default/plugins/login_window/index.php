<?php
if($this->getPage('page_live') == 'off' || !$this->getPage('page_live'))
	return;
elseif($this->isAdmin())
	return;
$View		=	$this->getPlugin('\nPlugins\Nubersoft\View');
$checkLogin	=	$this->getHelper('ValidateLoginState');
$valid		=	$checkLogin->validate($this->getPage("session_status"))->isRequired();
# This is passed by the passToNext() method in nFunctions
$useLayout	=	(!empty($this->data[0]))? $this->data[0] : false;
# Pass a layout path
if(is_file($useLayout))
	$View->useLayout($useLayout,'d');
echo $View->loginPage($valid);