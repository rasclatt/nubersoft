<?php
	
function printpre()
	{
		$settings	=	func_get_args();
		$count		=	func_num_args();
		$nDebug		=	new \Nubersoft\nDebug();
		return $nDebug->printPre($settings,$count);
	}