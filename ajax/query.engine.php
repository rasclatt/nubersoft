<?php
	include(__DIR__.'/../config.php');
	if(!is_admin())
		return;
		
	include(NBR_ROOT_DIR.'/core/engine/admintools/functions.components/function.create_form_component.php');
	
	echo create_form_component($_GET);
?>