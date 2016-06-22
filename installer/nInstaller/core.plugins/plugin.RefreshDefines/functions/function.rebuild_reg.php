<?php
function rebuild_reg()
	{
		$dir	=	CLIENT_DIR.'/settings/';
		$fName	=	'config-client.php';
		AutoloadFunction("create_define_file");
		$txt	=	(create_define_file(array("dir"=>$dir,"filename"=>$fName)))? 'rebuilt' : 'error';
		RegistryEngine::saveIncidental('rebuild_reg',$txt);
	}