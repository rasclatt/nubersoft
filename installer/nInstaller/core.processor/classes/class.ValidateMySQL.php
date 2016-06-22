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
						$filter[]		=	'users';
						$filter[]		=	'components';
						$filter[]		=	'system_settings';
						self::$tables	=	nApp::getTables();
						if($tables != 0) {
							foreach($tables as $table) {
								$new[]	=	(in_array($table,$filter))? 1 : 0;
							}
						}
						
						return	(array_sum($new) >= 3);
					}
					
					return false;
				}
		}