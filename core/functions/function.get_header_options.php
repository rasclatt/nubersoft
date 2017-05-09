<?php
/*Title: get_header_options()*/
/*Description: This function specifically returns the site header information.*/
	function get_header_options()
		{
			
			return (object) GetSitePrefs::$header['content'];
		}
?>