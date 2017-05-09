<?php
	
	class	AutoLoadRegister
		{
			private	$invalidReg;
			private	$apps;
			private	$useApp;
			private	$validApp;
			
			public	function __construct($invalidReg = false)
				{
					// default is false. Will populate if no registry file available
					$this->invalidReg	=	$invalidReg;
					// Set the app to false to clear it out
					$this->useApp	=	false;
					$this->validApp	=	false;
				}
			
			public	function getAppStatus()
				{
					return $this->useApp;
				}
			
			public	function getAttr($instr = false)
				{
					// If there is no registry file, just return false
					if($this->invalidReg) {
						$this->useApp	=	false;
						return $this;
					}
					// Get the instructions, formatted like so: replace/whatever
					$instructs	=	array_filter(explode("/",$instr));
					// There has to be at least two to continue
					if(count($instructs) != 2) {
						$this->useApp	=	false;
					}
					else {	
						// If there is an app array, set it back as well as assign it
						if(isset(NubeData::$settings->{$this->getSubAttr($instructs[0])}->{$instructs[1]}))
							$this->useApp	=	Safe::to_array(NubeData::$settings->{$this->getSubAttr($instructs[0])}->{$instructs[1]});
						// If no app exists, just return false
						else
							$this->useApp	=	false;
					}
					
					// If the app is not an array or there is unknown type, or there is no name invalid
					if(!is_array($this->useApp) || empty($this->useApp['type']) || empty($this->useApp['name'])) {
						RegistryEngine::saveError("autoload_initApp",array("error"=>"App is missing key ingredients (Invalid XML: needs ".Safe::encodeSingle("<classes>/<functions>").", ".Safe::encodeSingle("<class_Or_Nunction_Name>").", or just incorrect forming of XML in general."));
						return $this;
					}
						
					if($this->useApp['type'] == 'class') {
						if(!class_exists($this->useApp['name'])) {
							RegistryEngine::saveError("autoload_addClass",array("error"=>"Class is not pre-loaded. Check your registry ".Safe::encodeSingle("<dir>")));
							$this->useApp	=	false;
							return $this;
						}
					}
						
					$this->validApp	=	true;
					
					// Return for method chaining
					return $this;
				}
			// This will return the first part of the array key
			private	function getSubAttr($attr)
				{
					switch($attr) {
						case("onload"):
							return "__load/{$attr}";
							break;
						case("onlogin"):
							return "__login/{$attr}";
							break;
						case("afterlogin"):
							return "__login/{$attr}";
							break;
					}
				}
			// This will assemble the app
			public	function getApp()
				{
					if(!$this->validApp) {
						$this->useApp	=	false;
						return $this;
					}
						
					if($this->useApp['type'] == 'class') {
						
							$currApp	=	new $this->useApp['name']();
								
							if(empty($this->useApp['methods']) || empty($currApp)) {
								$this->useApp	=	false;
								return $this;
							}
									
							foreach($this->useApp['methods'] as $meth) {
									if(strpos($meth,"[") !== false) {
										$meth		=	rtrim($meth,"]");
										$useVals	=	explode("[",$meth);
										// Get the method back
										$meth		=	trim(array_shift($useVals));
									}
									
									if(!method_exists($this->useApp['name'],$meth)) {
										RegistryEngine::saveError("autoload_addMethod",array("error"=>"Method ({$this->useApp['name']}::{$meth}) does not exist"));
										continue;
									}
										
									if(!empty($useVals[0]))
										$currApp->$meth(explode(",",$useVals[0]));
									else
										$currApp->$meth();
								}
						}
					
					$this->useApp	=	false;
					return $this;
				}
		}