<?php
	class RegisterSetting
		{
			protected	static	$singleton;
			
			public	function __construct()
				{
					register_use(__METHOD__);
					
					if(!self::$singleton) {
							self::$singleton	=	$this;
							return self::$singleton;
						}
					else
						return self::$singleton;
				}
			
			public	function QuickSave($name = false,$value = false)
				{
					register_use(__METHOD__);
					
					$split	=	((!is_array($value) && !is_object($value)) && strpos($value,",") != false);
					NubeData::$settings	=	Safe::to_array(NubeData::$settings);
					
					if(!empty($name)) {
							if($split) {
									$exp	=	array_unique(explode(",",$value));
									NubeData::$settings[$name][$exp[0]][]	=	$exp[1];
								}
							else
								NubeData::$settings[$name]	=	$value;
						}
					
					NubeData::$settings	=	Safe::to_object(NubeData::$settings);
				}
			
			public	function SaveTo($type = 'settings')
				{	
					register_use(__METHOD__);
					AutoloadFunction("to_array,to_object");

					NubeData::$settings		=	(!isset(NubeData::$settings))? (object) array() : NubeData::$settings;
					NubeData::$errors		=	(!isset(NubeData::$errors))? (object) array() : NubeData::$errors;
					NubeData::$incidentals	=	(!isset(NubeData::$incidentals))? (object) array() : NubeData::$incidentals;
					
					switch($type) {
							case ('errors'):
								NubeData::$errors						=	Safe::to_array(NubeData::$errors);
								NubeData::$errors[$this->objname][]		=	(is_object($this->value))? Safe::to_array($this->value): $this->value;
								NubeData::$errors						=	Safe::to_object(NubeData::$errors);
								break;
							case ('incidentals'):
								NubeData::$incidentals						=	Safe::to_array(NubeData::$incidentals);
								NubeData::$incidentals[$this->objname][]	=	(is_object($this->value))? Safe::to_array($this->value): $this->value;
								NubeData::$incidentals						=	Safe::to_object(NubeData::$incidentals);
								break;
							default:
								// Convert to array
								NubeData::$settings	=	Safe::to_array(NubeData::$settings);
								
								// Array value
								if(is_object($this->value))
									$this->value	=	Safe::to_array($this->value);
								
								NubeData::$settings[$this->objname]	=	$this->value;
								/*
								
								// Is the settings already set
								$settingSet	=	(isset(NubeData::$settings[$this->objname]));
								// is the settings set and is an array
								$settingArr	=	($settingSet && is_array(NubeData::$settings[$this->objname]));
								// Is the intended stored value an array
								$valueArr	=	(is_array($this->value));
								// If the setting is not set, just set it, no problem
								if(!$settingSet) {
										
										NubeData::$settings[$this->objname]	=	$this->value;
									}
								// If set, lets do stuff
								else {
										// If what is currently set is an array, push
										if($settingArr)
											NubeData::$settings[$this->objname][]	=	$this->value;
										// If the value is not an array
										else {
												// Just create a new array and assign the first object to key 1
												$new[]	=	NubeData::$settings[$this->objname];
												// assign new value to key 2
												$new[]	=	$this->value;
												
												NubeData::$settings[$this->objname]	=	$new;
											}
									}
									*/
	
								NubeData::$settings	=	Safe::to_object(NubeData::$settings);
								
						}
					
					
					return $this;
				}
			
			
			public	function UseData($objname = false,$value = false)
				{
					register_use(__METHOD__);
					
					$this->objname	=	$objname;
					$this->value	=	(is_object($value))? Safe::to_array($value):$value;
					
					return $this;
				}
		}