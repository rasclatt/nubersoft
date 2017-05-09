<?php
/*Title: FetchConnection()*/
/*Description: This is the default method to retrieve the `PDO`/`MySQL` connection and it's supporting engines. It is best to just retrieve the `nQuery()` function to make updates to the `database`. The `FetchConnection()` function is used in the static `DatabaseConfig()` class which is the primary database engine.*/
/*Preferred nQuery Method: 
`AutoloadFunction('nQuery');
$query = nQuery();
$query->select("ID")->from("users")->fetch();`*/

	function FetchConnection($validate = false)
		{
			
			NubeData::$settings['action']	=	(isset($_POST['action']) && !empty($_POST['action']))? $_POST['action'] : false;
			
			// Fetch Database settings from class
			$_db_creds	=	new FetchCreds();
			$mysqlUser	=	base64_decode($_db_creds->_creds['user']);
			$mysqlPass	=	base64_decode($_db_creds->_creds['pass']);
			$mysqlDB	=	base64_decode($_db_creds->_creds['host']);
			$mysqlTable	=	base64_decode($_db_creds->_creds['data']);
			
			try {
					// connect to server and create a new data object
					$con		=	new PDO("mysql:host=$mysqlDB;dbname=$mysqlTable", $mysqlUser, $mysqlPass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
					// Assign error info for connection
					$_error		=	$con->errorInfo();
					$dbokay		=	array_filter($_error);
					
					if(isset($settings['validate']) && $settings['validate'] == true) {
							return (empty($dbokay))? true:false;
							$con	=	NULL;
							unset($con);
						}
						
					// Add to $con as overload
					$con->MySQLdb	=	$mysqlTable;
					// Save to nubedata
					NubeData::$settings['connection']	=	(object) array('database' => $con->MySQLdb);
					AutoloadFunction('site_valid');
					// Check if there are tables in the $con
					$valid		=	nApp::siteValid();
				}
			catch (PDOException $e) {
					global $_error;
					$_error['dbconnection']	=	'Connection Failure.';
				}
			
			$settings['con']		=	(!isset($_error['dbconnection']))? true:false;
			$settings['nubquery']	=	(!isset($_error['dbconnection']))? true:false;
			$settings['nubsql']		=	(!isset($_error['dbconnection']))? true:false;
			$settings['valid']		=	(!isset($_error['dbconnection']))? (($valid != 0)? true:false):false;
			$settings['sql']		=	(!isset($_error['dbconnection']))? true:false;
			
			NubeData::$settings['connection']->validate	=	$settings;
			
			return ($settings['con'] == true)? $con:false;
		}
?>