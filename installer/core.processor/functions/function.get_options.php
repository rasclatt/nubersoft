<?php
/*Title: get_options()*/
/*Description: This retrieves more site options.*/
	function get_options()
		{
			register_use(__FUNCTION__);
			$header		=	new HeaderCore();
			$header->Initialize();
			return $header->preferences;
		}
?>