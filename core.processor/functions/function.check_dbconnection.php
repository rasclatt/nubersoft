<?php
	function check_dbconnection($settings = false)
		{
			
			AutoloadFunction('is_admin,printpre,check_empty');
			if(is_admin()) {
					
					if($settings == false)
						return (DatabaseConfig::$con == false)? false:true;
					
					if(!function_exists('TestEmpty')) {
							function TestEmpty($value = false)	{
									if($value != false && !empty($value))
										return true;
								}
						}
						
					// Filter credentials
					$dbusername	=	(!empty($settings['dbusername']))? preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($settings['dbusername'])):false;
					$dbpassword	=	(!empty($settings['dbpassword']))? preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($settings['dbpassword'])):false;
					$dbhost		=	(!empty($settings['host']))? preg_replace('/[^0-9a-zA-Z\-\_\.]/','',trim($settings['host'])):false;
					$dbdatabase	=	(!empty($settings['database']))? preg_replace('/[^0-9a-zA-Z\-\_\.]/','',trim($settings['database'])):false;
					
					if(TestEmpty($dbusername) && TestEmpty($dbpassword) && TestEmpty($dbhost) && TestEmpty($dbhost)) {
							
							try {
									$tempcon	=	new PDO("mysql:host=$dbhost;dbname=$dbdatabase", $dbusername, $dbpassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
								}
							catch (PDOException $e)	{
									if($e) {
											$errors	=	$e->getMessage();
										}
								}
						}
					
					return (isset($errors))? false:true;
				}
				
			return false;
		}