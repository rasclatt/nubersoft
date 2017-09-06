<?php
$appendPath =	$content['appendPath'];
$cou 		=	$content['cou'];
$func		=	$content['func'];
$cacheDir	=	$this->getCacheFolder().DS.'pages'.DS;
$host		=	trim(str_replace(array('.','-','\_','http://','https://'),array(''),$this->localeUrl()),'/');
$state		=	(empty($this->getPageURI('is_admin')) || $this->getPageURI('is_admin') > 1)? 'base_view' : 'admin_view';
$toggled	=	(!empty($this->getDataNode('_SESSION')->toggle->edit))? 'is_toggled' : 'not_toggle';
$post		=	$this->getPost('action');
$get		=	$this->getGet('action');
$usePost	=	(is_string($post))? DS.md5($post) : '';
$useGet		=	(is_string($get))? DS.md5($get) : '';
$country	=	(!empty($this->getSession('LOCALE')))? trim($this->getSession('LOCALE'),'/') : $cou;
$tempBase	=	$this->getDataNode('site')->templates->template_site->dir;
$defPath	=	(!empty($this->getPageURI('full_path')))? trim(str_replace('/',DS,$this->getPageURI('full_path')),DS) : 'static';
$ID			=	(!empty($this->getPageURI('ID')))? $this->getPageURI('ID') : (($defPath == 'static')? 'error' : md5($defPath));
$loggedIn	=	($this->isLoggedIn())? 'loggedin' : 'loggedout';
$usergroup	=	(!empty($this->getSession('usergroup')))? $this->getSession('usergroup') : 'static';
$isSsl		=	($this->isSsl())? 'https' : 'http';
$group_id	=	(!empty($this->getSession('group_id')))? 'gid_'.$this->getSession('group_id') : 'gid_base';
$finalPath	=	$this->toSingleDs($cacheDir.DS.$host.DS.$country.DS.$isSsl.DS.$tempBase.DS.$defPath.DS.$loggedIn.$useGet.$usePost.DS.$toggled.DS.$state.DS.$usergroup.DS.$group_id.DS.$ID.$appendPath);

if(is_callable($func))
	echo $func($this,$finalPath);
else
	echo $finalPath;