<?php
if(!function_exists('is_admin'))
	return;
elseif(!is_admin())
	return;
$dir			=	(!empty(nApp::getDataNode('site')->cache_folder))? nApp::getDataNode('site')->cache_folder : NBR_ROOT_DIR.CACHE_DIR;
$valid_dir		=	is_dir($dir);
$valid_start	=	$valid_dir;
$try_delete		=	false;
if(\nApp::getRequest('action') != 'nbr_cache_delete')
	return;
	
if($valid_dir) {
	$try_delete	=	true;
	\DeleteCache::Delete($dir,DeleteCache::ADMIN,DeleteCache::KEEP_DIR,DeleteCache::SUPRESS_ERR);
}

$valid_dir	=	is_dir($dir);

nApp::saveIncidental('action',array('cache_delete'=>$valid_dir));