<?php
	function site_valid()
		{
			register_use(__FUNCTION__);
			return	(DatabaseConfig::$con != false);
		}
?>