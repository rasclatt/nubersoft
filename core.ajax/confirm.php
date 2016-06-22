<?php
include_once(__DIR__.'/../config.php');

if(!is_admin())
	exit;
	
AutoloadFunction("ajax_confirm",__DIR__.'/functions/');
echo ajax_confirm();