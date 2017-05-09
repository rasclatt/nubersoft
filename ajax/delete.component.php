<?php
include_once(__DIR__.'/../config.php');

if(!is_admin())
	return;
	
AutoloadFunction("ajax_delete_component",__DIR__.'/functions/');
echo ajax_delete_component();