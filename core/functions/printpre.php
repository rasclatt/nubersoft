<?php
function printpre()
	{
		$settings	=	func_get_args();
		$array		=	(isset($settings[0]))? $settings[0] : false;
		$options	=	(isset($settings[1]))? $settings[1] : false;
		$nDebug		=	new Nubersoft\nDebug();
		return $nDebug->printPre($array,$options);
	}