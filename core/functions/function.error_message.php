<?php
	function error_message($code = false)
		{
			$error['permission']	=	'You do not have proper access to view this content.';
			$error['loggedin']		=	'You must be logged in to view this content.';
			
			return	(isset($error[$code]))? $error[$code]:"Unknown Error";
		}
?>