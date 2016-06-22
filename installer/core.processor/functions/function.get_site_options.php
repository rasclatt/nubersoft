<?php
/*Title: get_site_options()*/
/*Description: This will retrieve all the `site` options only.*/
	function get_site_options()
		{
			register_use(__FUNCTION__);
			return (object) GetSitePrefs::$site['content'];
		}
?>