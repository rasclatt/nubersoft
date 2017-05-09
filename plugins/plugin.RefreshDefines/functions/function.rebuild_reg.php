<?php
/*
**	@description	This function will take the reg file and convert the <ondefine> to a config file
*/
function rebuild_reg()
	{
		$dir	=	NBR_CLIENT_DIR.'/settings/';
		$fName	=	'config-client.php';
		AutoloadFunction("create_define_file");
		$txt	=	(create_define_file(array("dir"=>$dir,"filename"=>$fName)))? 'rebuilt' : 'error';
		RegistryEngine::saveIncidental('rebuild_reg',$txt);
	}