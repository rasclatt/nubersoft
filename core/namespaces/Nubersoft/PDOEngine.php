<?php
/*
**	@method	connect() Implements the connection
**	@method	useCreds() Required for the db connection
**	@method	useOpts() Allows for PDO-specific settings
**	@method	getConnection() Returns connection
**
**	@use	DatabaseAdapter->PDOEngine->DatabaseEngine
*/
namespace Nubersoft;

class PDOEngine extends \Nubersoft\DatabaseAdapter
	{
		public		$defaultOpts	=	array();
		
		protected	$creds,
					$port;
		protected	$conAppend	=	array();
		
		private		$opts;
		private	static	$singleton,
						$con,
						$dbConn;
		
		public	function __construct()
			{
				if(!(self::$singleton instanceof \Nubersoft\DatabaseAdapter))
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
				if(self::$con instanceof \PDO) {
					return self::$con;
				}
				elseif(empty($this->creds)) {
					self::$con	=	false;
					return $this;
				}
					
				if(empty($this->opts))
					$this->opts	=	array(
						\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
						\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
						\PDO::ATTR_EMULATE_PREPARES => false
					);
				
				$this->port	=	(!empty($this->port))? $this->port : '3306';
				
				$append		=	(!empty($this->conAppend))? ";".implode("; ",$this->conAppend).";" : ""; 
				$str		=	"mysql:host=".$this->creds['host'].";dbname=".$this->creds['data'].';port='.$this->port.$append;
				try {
					self::$con	=	new \PDO($str, $this->creds['user'], $this->creds['pass'],$this->opts);
					
					if(empty(self::$con))
						throw new \Exception('Database connection has failed');
					
					return self::$con;

				} catch (PDOException $e) {
					// Save to nubedata
					nApp::call()->saveSetting('connection', array('database'=>$this->creds['data'],'health'=>false));
					nApp::call()->saveError('connection','Connection Failure.');
					self::$con	=	$this->con	=	false;
					
					if(nApp::call()->getFunction('is_admin')) {
						die(printpre($e->getMessage(),'{backtrace}'));
					}
				}
				
				return false;
			}
		
		public	function getConnection()
			{
				if(self::$con instanceof \PDO)
					return self::$con;
					
				return $this->connect();
			}
	}