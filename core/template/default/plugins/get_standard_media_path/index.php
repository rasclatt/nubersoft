<?php
# Fetch the content 
$path	=	$content;

if(!empty($path['state']))
	$state	=	$path['state'];
else
	$state	=	(empty($this->getPageURI('is_admin')) || $this->getPageURI('is_admin') > 1)? 'base_view' : 'admin_view';

if(!empty($path['toggled']))
	$toggled	=	$path['toggled'];
else
	$toggled	=	(!empty($this->getDataNode('_SESSION')->toggle->edit))? 'is_toggled' : 'not_toggle';
$post		=	$this->getPost('action');
$get		=	$this->getGet('action');
$usePost	=	(is_string($post))? DS.$post : '';
$useGet		=	(is_string($get))? DS.$get : '';
$country	=	(!empty($this->getSession('LOCALE')))? trim($this->getSession('LOCALE'),'/') : 'en';
$base		=	(!empty($path['base']))? $path['base'] : 'prefs';
$type		=	(!empty($path['type']))? $path['type'] : 'base';
$ext		=	(!empty($path['ext']))? '.'.$path['ext'] : '.json';
$cacheDir	=	$this->getCacheFolder();
$tempBase	=	$this->getDataNode('site')->templates->template_site->dir;
$defPath	=	(!empty($this->getPageURI('full_path')))? trim(str_replace('/',DS,$this->getPageURI('full_path')),DS) : 'static';
$ID			=	(!empty($this->getPageURI('ID')))? $this->getPageURI('ID') : (($defPath == 'static')? 'error' : md5($defPath));
$loggedIn	=	($this->isLoggedIn())? 'loggedin' : 'loggedout';
$usergroup	=	(!empty($this->getSession('usergroup')))? $this->getSession('usergroup') : 'static';
$isSsl		=	($this->isSsl())? 'https' : 'http';
echo $this->toSingleDs($cacheDir.DS.$isSsl.DS.$base.DS.$country.DS.$type.DS.$tempBase.DS.$defPath.DS.$loggedIn.$useGet.$usePost.DS.$toggled.DS.$state.DS.$usergroup.DS.$ID.$ext);