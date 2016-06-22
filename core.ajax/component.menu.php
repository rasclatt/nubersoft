<?php
include_once(__DIR__.'/../config.php');

if(!is_admin())
	exit;
AutoloadFunction("fetch_component_options,render_component_settings,ajax_component_menu",__DIR__.'/functions/');
echo ajax_component_menu();