<?php
function create_htaccess_directive()
	{
		$arr			=	NuberEngine::getRegFile();
		$protect		=	(!empty($arr['protectdirectory']['var']))? $arr['protectdirectory']['var'] : array();
		$unprotect		=	(!empty($arr['unprotectdirectory']['var']))? $arr['unprotectdirectory']['var'] : array();
		$unprotectplus	=	(!empty($arr['unprotectset']['var']))? $arr['unprotectset']['var'] : array();
		// No browser access
		$protect		=	(is_array($protect))? $protect : array($protect);
		// Remove htaccess
		$unprotect		=	(is_array($unprotect))? $unprotect : array($unprotect);
		// Add browser access
		$unprotectplus	=	(is_array($unprotectplus))? $unprotectplus : array($unprotectplus);
		
		return array("protect"=>$protect,"unprotect"=>$unprotect,"unprotectset"=>$unprotectplus);
	}