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
		public		$defaultOpts	=	array(	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
												PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
												PDO::ATTR_EMULATE_PREPARES => false);
		
		protected	$creds;
		protected	$conAppend	=	array();
		
		private		$opts;
		
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
					
		public	function connect()
			{
				if(empty($this->creds))
					return $this->con	=	false;
				
				$this->opts	=	(!empty($this->opts))? $this->opts : false;
				$append		=	(!empty($this->conAppend))? ";".implode("; ",$this->conAppend).";" : "";
				$str		=	"mysql:host=".$this->creds['host'].";dbname=".$this->creds['data'].$append;
				try {
					$this->con	=	(empty($this->opts))?
									new PDO($str, $this->creds['user'], $this->creds['pass']):
									new PDO($str, $this->creds['user'], $this->creds['pass'],$this->opts);

				} catch (PDOException $e) {
					// Save to nubedata
					RegistryEngine::saveSetting('connection', array('database'=>$this->creds['data'],'health'=>false));
					RegistryEngine::saveError('connection','Connection Failure.');
					$this->con	=	false;
				}
				
				return $this;
			}
	}