<?php
include_once(__DIR__.'/../config.php');
AutoloadFunction("check_empty");

if(!check_empty($_POST,'content'))
	return;
	
AutoloadFunction("ajax_display_live_type",__DIR__.'/functions/');
echo ajax_display_live_type();