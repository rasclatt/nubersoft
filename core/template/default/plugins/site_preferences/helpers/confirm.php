<?php
include_once(__DIR__.'/../config.php');

if(!is_admin())
	exit;
	
autoload_function("ajax_confirm",__DIR__.'/functions/');
echo ajax_confirm();