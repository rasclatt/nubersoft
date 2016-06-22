<?php
/*Title: DatabaseConfig*/
/*Description: This class implements the DatabseDriver interface which only requires the connect() method. This is the default driver, which is a PDO MySQL driver.*/
	class DatabaseConfig implements DatabaseDriver
		{
			public	static	$nubsql;
			public	static	$nuber;
			public	static	$query;
			public	static	$con;
			public	static	$valid;
			public	static	$database;
			public	static	$dbOpts;
			
			const	REFRESH_OPTS	=	true;
			const	NEW_DBC			=	"new_conn";
			
			public	static	function connect($settings = false)
				{
					// Check if the error reporting is on
					AutoloadFunction('error_check');
					if(error_check()) {
						//ini_set("display_errors",1);
						//error_reporting(E_ALL);
					}
					else {
						ini_set("display_errors",0);
						error_reporting(0);
					}
					if(!empty(self::$con) && is_object(self::$con)) {	
						return self::$con;
					}
					// Valid credentials default
					$thisConn	=	false;
					// Check for connection optiosn
					if(!empty($settings['options']) && is_array($settings['options']))
						self::$dbOpts	=	$settings['options'];
					else
						self::SetDatabaseAttr();
					// Set the default character	
					$charSet	=	(!isset($settings['charset']))? "useUtf8" : $settings['charset'];
					// Set default for database status	
					self::$database	=	false;
					// Fetch database credentials
					$credentials	=	new FetchCreds();
					// Check that credentials are there
					if(!empty($credentials->_creds)) {
						// Assign all the credentials from the file
						$creds['user']	=	base64_decode($credentials->_creds['user']);
						$creds['pass']	=	base64_decode($credentials->_creds['pass']);
						$creds['host']	=	base64_decode($credentials->_creds['host']);
						$creds['data']	=	base64_decode($credentials->_creds['data']);
						$thisConn		=	true;
					}
					
					// If the credentials are not set, return false and set errors
					if(!$thisConn) {
						// Save response to global array
						RegistryEngine::saveSetting('connection', array('database'=>false,'health'=>false));
						RegistryEngine::saveError('connection', array('success'=>false,'error'=>'incomplete/invalid db creds'));
						return false;
					}
					// Try connecting to database
					try {
							self::$database		=	$creds['data'];
							$options['creds']	=	$creds;
							$options['opts']	=	self::$dbOpts;
							$options['instr']	=	array($charSet=>false);
							// connect to server and create a new data object
							self::$con			=	DatabaseEngine::connect($options);
							// Assign error info for connection
							$_error				=	(!empty(self::$con))? self::$con->errorInfo() : false;	
							$dbokay				=	array_filter($_error);
							// Save to nubedata
							if(is_object(self::$con))
								RegistryEngine::saveSetting('connection', array('database'=>$creds['data'],'health'=>true));
							else
								RegistryEngine::saveSetting('connection', array('database'=>$creds['data'],'health'=>false));
						}
					catch (PDOException $e) {
							// Save to nubedata
							RegistryEngine::saveSetting('connection', array('database'=>$creds['data'],'health'=>false));
							RegistryEngine::saveError('connection','Connection Failure.');
							self::$con	=	false;
						}
					// Add error handling
					self::ValidateSettings(self::$con);
					return self::$con;
				}
			
			public	static	function ValidateSettings($dbConn)
				{
					
					self::$query	=	self::$valid	=	false;
					// Check if valid connection
					$database		=	ValidateMySQL::CheckDatabase($dbConn);
					if($database != false) {
						//self::$query	=	new ConstructMySQL();
						self::$valid	=	true;
					}
					
					if(!self::$valid) {
						RegistryEngine::saveIncidental('database','Connection Error.');
						// Notify that sql is not working
						NubeData::$settings->sql	=	false;
						// See if user table exists
						if(self::$con == false)
							$_checkUsers	=	0;
						else {
							AutoloadFunction('nQuery');
							$nubquery		=	nQuery();
							$_checkUsers	=	$nubquery	->select("COUNT(*) as count")
															->from("users")
															->fetch();
						}

						if(is_admin()) {
							// If no users are return or table does not exist.
							if($_checkUsers == 0) {
								RegistryEngine::saveIncidental('database', array('con_admin'=>'Database Failure on line '.__LINE__.'=>'.__FILE__));									
								include_once(RENDER_LIB.'/classes/installer/connect.remote.php');
								$_createTable	=	FetchRemoteTable::Create(self::$nubsql,'all');
								
								// $_createTable returns opposite of expected
								return	(!$_createTable);
							}
						}

						// Return failed
						return false;
					}
				}
			
			public	static	function SetDatabaseAttr($value = false,$refresh = false)
				{
					// Save defaults for connection
					if(!is_array($value) || empty($value)) {						
						self::$dbOpts[PDO::ATTR_ERRMODE]			=	PDO::ERRMODE_EXCEPTION;
						self::$dbOpts[PDO::ATTR_DEFAULT_FETCH_MODE]	=	PDO::FETCH_ASSOC;
						self::$dbOpts[PDO::ATTR_EMULATE_PREPARES]	=	false;
					}
					else {
						if(empty(self::$dbOpts) || $refresh)
							self::$dbOpts	=	array();
						
						foreach($value as $DBKey => $DBValue)
							self::$dbOpts[$DBKey]	=	$DBValue;
					}
					
					return self::$dbOpts;
				}
		}