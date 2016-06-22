<?php
/*Title: get_now_status()*/
/*Description: This function checks if the state of the database connection is working as well if the user is logged out. This function is for a first-time run situation.*/
	function get_now_status()
		{
			register_use(__FUNCTION__);
			return	(isset(NubeData::$settings->engine->database) && NubeData::$settings->user->loggedin == false)? true: false;
		}
?>