<?php
/*Title: nQuery()*/
/*Description: This function is the main Query Engine. Thus far, supports MySQL and MSSQL connections.*/
/*Example:

`$nubquery = nQuery();`
*/
function nQuery($settings = false,$override = false)
	{
		// Use either the persistant connection of the injected connection
		$db	=	(!is_object($settings))? DatabaseConfig::getConnection() : $settings;
		if($override) {
			DatabaseConfig::$con	=	null;
			DatabaseConfig::$con	=	$db;
		}
		
		if($db) {
			return new ConstructMySQL($db);
		}
		
		return   false;
	}