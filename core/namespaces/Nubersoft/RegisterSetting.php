<?php
namespace Nubersoft;

class RegisterSetting extends \Nubersoft\nFunctions
	{
		public	function quickSave($name = false,$value = false)
			{
				$split	=	((!is_array($value) && !is_object($value)) && strpos($value,",") != false);
				NubeData::$settings	=	$this->toArray(NubeData::$settings);
				
				if(!empty($name)) {
					if($split) {
						$exp	=	array_unique(explode(",",$value));
						NubeData::$settings[$name][$exp[0]][]	=	$exp[1];
					}
					else
						NubeData::$settings[$name]	=	$value;
				}
				
				NubeData::$settings	=	$this->toObject(NubeData::$settings);
			}
		
		public	function saveTo($type = 'settings')
			{
				NubeData::$settings		=	(!isset(NubeData::$settings))? (object) array() : NubeData::$settings;
				NubeData::$errors		=	(!isset(NubeData::$errors))? (object) array() : NubeData::$errors;
				NubeData::$incidentals	=	(!isset(NubeData::$incidentals))? (object) array() : NubeData::$incidentals;
				
				switch($type) {
					case ('errors'):
						NubeData::$errors						=	$this->toArray(NubeData::$errors);
						NubeData::$errors[$this->objname][]		=	(is_object($this->value))? $this->toArray($this->value): $this->value;
						NubeData::$errors						=	Safe::to_object(NubeData::$errors);
						break;
					case ('incidentals'):
						NubeData::$incidentals						=	$this->toArray(NubeData::$incidentals);
						NubeData::$incidentals[$this->objname][]	=	(is_object($this->value))? $this->toArray($this->value): $this->value;
						NubeData::$incidentals						=	$this->toObject(NubeData::$incidentals);
						break;
					default:
						// Convert to array
						NubeData::$settings	=	$this->toArray(NubeData::$settings);
						
						// Array value
						if(is_object($this->value))
							$this->value	=	$this->toArray($this->value);
						
						if(isset(NubeData::$settings[$this->objname])) {
							$sArr	=	NubeData::$settings[$this->objname];
							
							if(is_array($this->value))
								NubeData::$settings[$this->objname]	=	array_merge($sArr,$this->value);
							else {
								if(is_array(NubeData::$settings[$this->objname]))
									NubeData::$settings[$this->objname][]	=	$this->value;
								else
									NubeData::$settings[$this->objname]	=	$this->value;	
							}
						}
						else
							NubeData::$settings[$this->objname]	=	$this->value;

						NubeData::$settings	=	$this->toObject(NubeData::$settings);
				}
				
				return $this;
			}
		
		public	function clearDataNode($key,$type="settings")
			{
				if(isset(NubeData::${$type}->{$key}))
					unset(NubeData::${$type}->{$key});
				
				return $this;
			}
		
		public	function useData($objname = false,$value = false)
			{
				$this->objname	=	$objname;
				$this->value	=	(is_object($value))? $this->toArray($value) : $value;
				
				return $this;
			}
	}