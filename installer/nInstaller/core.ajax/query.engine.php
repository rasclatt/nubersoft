<?php
	include(__DIR__.'/../dbconnect.root.php');
	if(!is_admin())
		return;
		
	include(ROOT_DIR.'/core.processor/engine/admintools/functions.components/function.create_form_component.php');
	
	echo create_form_component($_GET);
?>