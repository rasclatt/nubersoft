<?php
/*
**	@method	usePDO() Instantiates the PDO driver
**	@method	connect() Accepts the credentials and returns driver connection
**
**	@use	DatabaseAdapter->PDOEngine->DatabaseEngine
*/
class DatabaseEngine
	{
		const	DEFAULT_TYPE	=	'pdo';
		private	$con;
		// @param [array] $creds Database host,data,user,pass, options, driver type, and any extra instruction
		public	static	function connect($settings = false)
			{
				$creds	=	(!empty($settings['creds']))? $settings['creds'] : false;
				$opts	=	(!empty($settings['opts']))? $settings['opts'] : array();
				$instr	=	(!empty($settings['instr']))? $settings['instr'] : false;
				$type	=	(!empty($settings['type']))? $settings['type'] : self::DEFAULT_TYPE;
				
				if(!$creds)
					return false;
					
				switch($type) {
					default:
						return self::usePDO($creds,$opts,$instr);
				}
			}
		// @param [array] $creds Database host,data,user,pass
		// @param [array] $opts Custom options for the PDO connection
		private	static	function usePDO($creds,$opts,$instr = false)
			{	
				$pdoEng	=	new PDOEngine();
				
				if(is_array($instr)) {
					foreach($instr as $method => $vals) {
						if(empty($vals))
							$pdoEng->{$method}();
						else
							$pdoEng->{$method}($vals);
					}
				}
				
				return $pdoEng	->useCreds($creds)
								->useOpts($opts)
								->connect()
								->getConnection();
			}
	}