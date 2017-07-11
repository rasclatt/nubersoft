<?php
include_once(__DIR__.'/../config.php');

if(!is_admin())
	return;

autoload_function("ajax_edit_folderfiles",__DIR__.'/functions/');
echo ajax_edit_folderfiles();