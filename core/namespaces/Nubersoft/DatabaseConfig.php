<?php
/*Title: DatabaseConfig*/
/*Description: This class implements the DatabseDriver interface which only requires the connect() method. This is the default driver, which is a PDO MySQL driver.*/
namespace Nubersoft;

class DatabaseConfig extends \Nubersoft\nApp implements \Nubersoft\DatabaseDriver
	{
		public	static	$con,
						$valid,
						$database,
						$dbOpts;
		
		private	static	$nApp;
		
		const	REFRESH_OPTS	=	true;
		const	NEW_DBC			=	"new_conn";
		
		public	function __construct()
			{
				self::$nApp	=	$this;
				
				return parent::__construct();
			}
		
		public	static	function connect($settings = false)
			{
				if(self::$con instanceof \PDO)
					return self::$con;
				
				# Valid credentials default
				$thisConn	=	false;
				# Check for connection optiosn
				if(!empty($settings['options']) && is_array($settings['options']))
					self::$dbOpts	=	$settings['options'];
				else
					self::setDatabaseAttr();
				# Set the default character	
				$charSet	=	(!isset($settings['charset']))? "useUtf8" : $settings['charset'];
				# Set default for database status	
				self::$database	=	false;
				# Fetch database credentials
				$credentials	=	new FetchCreds();
				# Check that credentials are there
				if(!empty($credentials->getCreds()->returnCreds())) {
					# Assign all the credentials from the file
					$creds['user']	=	$credentials->getUser();
					$creds['pass']	=	$credentials->getPass();
					$creds['host']	=	$credentials->getHost();
					$creds['data']	=	$credentials->getData();
					$thisConn		=	true;
				}
				# If the credentials are not set, return false and set errors
				if(!$thisConn) {
					# Save response to global array
					self::$nApp->saveSetting('connection', array('database'=>false,'health'=>false));
					self::$nApp->saveError('connection', array('success'=>false,'error'=>'incomplete/invalid db creds'));
					return false;
				}
				# Try connecting to database
				try {
					self::$database		=	$creds['data'];
					$options['creds']	=	$creds;
					$options['opts']	=	self::$dbOpts;
					$options['instr']	=	array($charSet=>false);
					# connect to server and create a new data object
					self::$con			=	DatabaseEngine::connect($options);
					# Assign error info for connection
					$_error				=	(!empty(self::$con))? self::$con->errorInfo() : array();	
					$dbokay				=	array_filter($_error);
					# Save to nubedata
					self::$nApp->saveSetting('connection', array('database'=>$creds['data'],'health'=>(self::$con instanceof \PDO)));
				}
				catch (\PDOException $e) {
					# Save to nubedata
					self::$nApp->saveSetting('connection', array('database'=>$creds['data'],'health'=>false));
					self::$nApp->saveError('connection','Connection Failure.');
					self::$con	=	false;
				}
				# Add error handling
				self::validateSettings(self::$con);
				
				return self::$con;
			}
		
		public	static	function validateSettings($dbConn)
			{
				self::$valid	=	false;
				# Check if valid connection
				$database		=	ValidateMySQL::CheckDatabase($dbConn);
				if($database != false) {
					//self::$query	=	new ConstructMySQL();
					self::$valid	=	true;
				}
				
				if(!self::$valid) {
					self::$nApp->saveIncidental('database','Connection Error.');
					# Notify that sql is not working
					\Nubersoft\NubeData::$settings->sql	=	false;
					# See if user table exists
					if(self::$con == false)
						$_checkUsers	=	0;
					else {
						$tables			=	self::$nApp->toArray(self::$nApp->getTables());
						
						if(is_array($tables) && in_array('users',$tables)) {
							$nubquery		=	self::$nApp->nQuery();
							$_checkUsers	=	$nubquery	->select("COUNT(*) as count")
															->from("users")
															->fetch();
						}
						else {
							$_checkUsers	=	0;
						}
					}
					
					if(self::$nApp->isAdmin()) {
						# If no users are return or table does not exist.
						if($_checkUsers == 0) {
							self::$nApp->saveIncidental('database', array('con_admin'=>'Database Failure on line '.__LINE__.'=>'.__FILE__));									
							
							self::$nApp->getHelper('CoreMySQL')
								->installAllTables()
								->installAllRows();
						}
					}
					# Return failed
					return false;
				}
			}
		
		public	static	function setDatabaseAttr($value = false,$refresh = false)
			{
				# Save defaults for connection
				if(!is_array($value) || empty($value)) {						
					self::$dbOpts[\PDO::ATTR_ERRMODE]				=	\PDO::ERRMODE_EXCEPTION;
					self::$dbOpts[\PDO::ATTR_DEFAULT_FETCH_MODE]	=	\PDO::FETCH_ASSOC;
					self::$dbOpts[\PDO::ATTR_EMULATE_PREPARES]		=	false;
				}
				else {
					if(empty(self::$dbOpts) || $refresh)
						self::$dbOpts	=	array();
					
					foreach($value as $DBKey => $DBValue)
						self::$dbOpts[$DBKey]	=	$DBValue;
				}
				
				return self::$dbOpts;
			}
		
		public	static	function getConnection()
			{
				if(self::$con instanceof PDO)
					return self::$con;
				
				return self::connect();
			}
		
		public	function init()
			{
				self::getConnection();
			}
			
		public	function dbHealth()
			{
				# Store Database Name
				$this->saveSetting('engine',array('dbname'=>$this->getDbName()));
				# Send verification that server is working
				$this->saveSetting('engine',array('sql'=>$this->siteValid()));
				# If live status has not yet been determined by now, set it to offline
				$this->saveSetting('engine',array('site_live'=>$this->siteLive()));
			}
	}