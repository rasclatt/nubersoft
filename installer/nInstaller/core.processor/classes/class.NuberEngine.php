<?php
	class	NuberEngine
		{
			private	static	$nubstance;
			private	static	$data;
			
			private	function __construct()
				{
				}
			
			public	static	final function Init()	
				{
					// Create base core engine
					return new Nuberizer();
				}
			
			public	static	function callPlugin($name)
				{
					if($name == 'core')
						return new PluginEngine(__DIR__.'/../engine/plugins');
				}
			/*
			** @description - Load registry file into xml object (if possible)
			*/
			private	static	function getXML($get = false)
				{
					if(!$xml = @simplexml_load_string(trim($get)))
						throw new Exception("There was an error processing xml contents. Check your xml carefully!");
					
					return (!empty($xml))? $xml : false;
				}
				
			public	static	function getRegFile($filename = false)
				{
					$filename	=	(!empty($filename) && is_file($filename))? $filename : CLIENT_DIR."/settings/registry.xml";
					try {
								// Try and get contents of file
						$get		=	@file_get_contents($filename);
						// Try to process it with xml processor
						$data		=	self::getXML($get);
					} catch (Exception $e) {
	
					}
					// If data is not empty, convert to an array
					return (!empty($data))? Safe::to_array($data) : false;
				}
			/*
			** @description - Load registry file if available and process it
			*/
			public	static function getRegistry($type = 'onload',$filename = false)
				{
					// Try and retrieve registry xml file
					$filename	=	(!empty($filename) && is_file($filename))? $filename : CLIENT_DIR."/settings/registry.xml";
					// See if registry file is available, if not send back registry and note the warning
					if(!is_file($filename)) {
							RegistryEngine::saveIncidental("autoloadRegistry","nofile");
							return self::getRegList();
						}
					// Try and get the registry
					try	{
							// Try and get contents of file
							$get		=	@file_get_contents($filename);
							// Try to process it with xml processor
							$data		=	self::getXML($get);
							// If data is not empty, convert to an array
							if(!empty($data))
								$data	=	Safe::to_array($data);
							// If empty, send back registry
							if(empty($data))
								return self::getRegList();
							// Check if value is set
							if(isset($data[$type])) {
								// If there are classes, run through those
								if(!empty($data[$type]['classes'])) {
									// Loop through classes
									foreach($data[$type]['classes'] as $class => $vals) {
										// If the directory is not blank (where the class can be found to include)
										if(!empty($vals['dir']) && is_file($inc = str_replace("//","/",ROOT_DIR."/".$vals['dir']."/class.".$class.".php")))
											// Include the directory
											include_once($inc);
										// Check if there are methods included in the class (construct runs by default)
										$methods				=	(!empty($vals['method']))? $vals['method']: false;
										// Check if there are is only one, if so, make an array
										if(!is_array($methods))
											$methods	=	array($methods);
										// Check to see if there are any instructions with this class
										$instr	=	(!empty($vals['instr']))? $vals['instr']: false;
										// Create an array to send to registry
										$instructions[str_replace("/","_",$instr)]	=	array("instr"=>$instr,
																								"methods"=>$methods,
																								"type"=>"class",
																								"name"=>$class);
									}
									// Create a general __load registry
									RegistryEngine::saveSetting("__load",array($type => array($class => array("instr"=>$instr,"methods"=>$methods))));
									// Create a usable registry which apps can be created from
									RegistryEngine::saveSetting("__load/{$type}",$instructions);
								}

								// Check if there are any includes
								if(!empty($data[$type]['includes'])) {
									array_map(function($v){
											$file = str_replace("//","/",ROOT_DIR."/".$v);
											if(is_file($file))
												include($file);
											
										},$data[$type]['includes']);
								}
							}
						}
					catch (Exception $e) {
							// If there is a problem (based likely with the xml reader, save to registry)
							RegistryEngine::saveError("autoload_generalError", array("message" => $e->getMessage()));
						}
						
					// Pass the register class back by default or the check function will produce fatal error 
					AutoloadFunction("register_exists");
					return self::getRegList(register_exists());
				}
			/*
			** @description - Loads the registry class
			*/
			public	static	function getRegList()
				{
					AutoloadFunction("register_exists");
					// Return the registry engine. If $isFile filled, then no registry file is available
					return new AutoLoadRegister(register_exists());
				}
		}