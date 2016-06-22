<?php
/*Title: get_options()*/
/*Description: This retrieves more site options.*/
	function get_options()
		{
			
			$header		=	new HeaderCore();
			$header->Initialize();
			return $header->preferences;
		}
?>