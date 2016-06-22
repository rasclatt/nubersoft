<?php
	// Allows setting loading
	class NubeData
		{
			public	static	$settings		=	array();
			public	static	$errors			=	array();
			public	static	$incidentals	=	array();
			private	static	$properties		=	array();
			
			public	function __get($property)
				{
					register_use(__METHOD__);
					return (isset(self::$properties[$property]))? self::$properties[$property]:false;
				}
			
			public	function __set($property,$value)
				{
					register_use(__METHOD__);
					self::$properties[$property]	=	$value;
				}
			
			public	function __isset($property)
				{
					register_use(__METHOD__);
					return	(isset(self::$properties[$property]))? true:false;			
				}
		
			// Used to validate template directory/files
			public	static function ActiveElement($dir = false,$file = false)
				{
					register_use(__METHOD__);
					// If input is empty -> false
					if(($dir == false))
						return false;
					// If input is not a directory -> false
					elseif(!is_dir($dir))
						return false;

					$valid_dir	=	scandir($dir);
					// Include file if is in directory
					if(in_array($file,$valid_dir))
						return true;
				}
		}