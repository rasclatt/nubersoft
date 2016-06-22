<?php
include_once(__DIR__.'/../dbconnect.root.php');

if(!is_admin())
	return;

AutoloadFunction("ajax_edit_folderfiles",__DIR__.'/functions/');
echo ajax_edit_folderfiles();