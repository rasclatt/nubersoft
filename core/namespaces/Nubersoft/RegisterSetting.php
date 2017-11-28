<?php
namespace Nubersoft;

class RegisterSetting extends \Nubersoft\nFunctions
{
	public	function quickSave($name = false,$value = false)
	{
		$split	=	((!is_array($value) && !is_object($value)) && strpos($value,",") != false);
		parent::$settings	=	$this->toArray(parent::$settings);

		if(!empty($name)) {
			if($split) {
				$exp	=	array_unique(explode(",",$value));
				parent::$settings[$name][$exp[0]][]	=	$exp[1];
			}
			else
				parent::$settings[$name]	=	$value;
		}

		parent::$settings	=	$this->toObject(parent::$settings);
	}

	public	function saveTo($type = 'settings')
	{
		parent::$settings		=	(!isset(parent::$settings))? (object) array() : parent::$settings;
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
				parent::$settings	=	$this->toArray(parent::$settings);

				// Array value
				if(is_object($this->value))
					$this->value	=	$this->toArray($this->value);

				if(isset(parent::$settings[$this->objname])) {
					$sArr	=	parent::$settings[$this->objname];

					if(is_array($this->value))
						parent::$settings[$this->objname]	=	(is_array($this->toArray($sArr)))? array_merge($this->toArray($sArr),$this->value) : $this->value;
					else {
						if(is_array(parent::$settings[$this->objname]))
							parent::$settings[$this->objname][]	=	$this->value;
						else
							parent::$settings[$this->objname]	=	$this->value;	
					}
				}
				else
					parent::$settings[$this->objname]	=	$this->value;

				parent::$settings	=	$this->toObject(parent::$settings);
		}

		return $this;
	}

	public	function clearDataNode($key,$type="settings")
	{
		if($type == 'settings') {
			if(isset(parent::$settings->{$key}))
				unset(parent::$settings->{$key});
		}
		else {
			if(isset(NubeData::${$type}->{$key}))
				unset(NubeData::${$type}->{$key});
		}
		
		return $this;
	}

	public	function useData($objname = false,$value = false)
	{
		$this->objname	=	$objname;
		$this->value	=	(is_object($value))? $this->toArray($value) : $value;

		return $this;
	}
}