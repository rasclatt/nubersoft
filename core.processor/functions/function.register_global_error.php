<?php
	function register_global_error($arg1=false,$arg2=false)
		{
			
			$values	=	func_get_args();
			
			if($values[0] == false)
				return false; 
			
			global $_error;
			$error[$values[0]]	=	$values[1];
		}
?>