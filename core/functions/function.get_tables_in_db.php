<?php
	function get_tables_in_db($table = false)
		{
			
			
			if(isset(ValidateMySQL::$tables) && ValidateMySQL::$tables != false) {
					$tables	=	ValidateMySQL::$tables;
					if(!empty($table) && !empty(ValidateMySQL::$tables))
						return (in_array($table,$tables))? $table:false;
						
					return ValidateMySQL::$tables;
				}
			else
				return 'false';
		}
?>