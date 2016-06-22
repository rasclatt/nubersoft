<?php
/*Title: ValidateMySQL*/
/*Description: This class just validates that there are enough tables in the database to sustain a viable nUberSoft Framework.*/
	class ValidateMySQL implements DatabaseValidator
		{
			public	static	$tables;
			public	static	function CheckDatabase($con = false)
				{
					// Create container for tables in database
					self::$tables	=	false;
					$val			=	nApp::getConStatus();
					
					if($val) {
						$filter[]	=	'users';
						$filter[]	=	'components';
						$filter[]	=	'system_settings';

						AutoloadFunction("nQuery");
						$nubquery	=	nQuery();
						$tables		=	$nubquery->fetchTablesInDB(nApp::getDbName())->fetch();
						if($tables != 0) {
							foreach($tables as $rows) {
								$table			=	$rows['Tables_in_'.nApp::getDbName()];
								$new[]			=	(in_array($table,$filter))? 1 : 0;
								self::$tables[]	=	$table;
							}
						}
						
						return	(array_sum($new) >= 3);
					}
					
					return false;
				}
		}