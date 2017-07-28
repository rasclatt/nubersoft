<?php
include_once(__DIR__.'/../config.php');
autoload_function("check_empty");

if(!check_empty($_POST,'action'))
	return;

autoload_function("ajax_check_user",__DIR__.'/functions/');
echo ajax_check_user();