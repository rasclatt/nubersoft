<?php
/*Title: FetchTables()*/
/*Description: This function is used to return all tables in the database. Requires `nQuery()`*/

	function FetchTables()
		{
			
			
			if(isset(NubeData::$settings->tables) && !empty(NubeData::$settings->tables))
				return	(array) NubeData::$settings->tables;
			
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			$creds		=	new FetchCreds();
			
			if(!isset($creds->_creds['data']))
				return false;
			
			$db			=	base64_decode($creds->_creds['data']);
			$results	=	$nubquery->fetchTablesInDB($db)->fetch();
			
			if($results != 0) {
					foreach($results as $object)
						$tables[]	=	$object['Tables_in_'.$db];
					
					if(isset($tables))
						return $tables;
				}
		}
?>