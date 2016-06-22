<?php
/*Title: get_header_options()*/
/*Description: This function specifically returns the site header information.*/
	function get_header_options()
		{
			register_use(__FUNCTION__);
			return (object) GetSitePrefs::$header['content'];
		}
?>