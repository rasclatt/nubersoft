<?php
/*
**	@method	connect() Implements the connection
**	@method	useCreds() Required for the db connection
**	@method	useOpts() Allows for PDO-specific settings
**	@method	getConnection() Returns connection
**
**	@use	DatabaseAdapter->PDOEngine->DatabaseEngine
*/
class PDOEngine extends DatabaseAdapter
	{
		public		$con;
		public		$defaultOpts	=	array();
		
		protected	$creds,
					$port;
		protected	$conAppend	=	array();
		
		private		$opts;
		private	static	$singleton,
						$dbConn;
		
		public	function __construct()
			{
				if(empty(self::$singleton))
					self::$singleton	=	$this;
				
				return self::$singleton;
			}
		
		public	function useOpts($array)
			{			
				$this->opts	=	(is_array($array))? $array : self::$defaultOpts;
				return $this;
			}
		
		public	function useUtf8()
			{
				$this->conAppend[]	=	'charset=utf8';
				return $this;
			}
		
		public	function setPort($port = '3306')
			{
				$this->port	=	$port;
				return $this;
			}

		public	function connect()
			{
				if(empty($this->creds)) {
					self::$dbConn	=	$this->con	=	false;
					return $this;
				}
				
				if(self::$dbConn instanceof PDO) {
					$this->con	=	self::$dbConn;
					return $this;
				}
					
				$this->opts	=	(!empty($this->opts))? $this->opts : array(	\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
																			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
																			\PDO::ATTR_EMULATE_PREPARES => false);
				
				$this->port	=	(!empty($this->port))? $this->port : '3306';
				
				$append		=	(!empty($this->conAppend))? ";".implode("; ",$this->conAppend).";" : "";
				$str		=	"mysql:host=".$this->creds['host'].";dbname=".$this->creds['data'].';port='.$this->port.$append;
				try {
					$this->con	=	(empty($this->opts))?
									new \PDO($str, $this->creds['user'], $this->creds['pass']):
									new \PDO($str, $this->creds['user'], $this->creds['pass'],$this->opts);
					
					self::$dbConn	=	$this->con;

				} catch (PDOException $e) {
					// Save to nubedata
					\nApp::saveSetting('connection', array('database'=>$this->creds['data'],'health'=>false));
					\nApp::saveError('connection','Connection Failure.');
					self::$dbConn	=	$this->con	=	false;
				}
				
				return $this;
			}
	}