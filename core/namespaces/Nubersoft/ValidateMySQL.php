<?php
/*Title: ValidateMySQL*/
/*Description: This class just validates that there are enough tables in the database to sustain a viable Nubersoft Framework.*/
namespace Nubersoft;

class ValidateMySQL implements \Nubersoft\DatabaseValidator
	{
		public	static	$tables;
		public	static	function CheckDatabase($con = false)
			{
				// Create container for tables in database
				self::$tables	=	false;
				$val			=	nApp::call()->getConStatus();
				$new			=	array();
				if($val) {
					$filter[]		=	'users';
					$filter[]		=	'components';
					$filter[]		=	'system_settings';
					self::$tables	=	nApp::call()->getTables();
					if(self::$tables != 0) {
						foreach(self::$tables as $table) {
							$new[]	=	(in_array($table,$filter))? 1 : 0;
						}
					}
					
					return	(array_sum($new) >= 3);
				}
				
				return false;
			}
	}