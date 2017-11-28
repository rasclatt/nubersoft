<?php
namespace Nubersoft;

abstract class	Singleton
{
	protected	static	$singleton;
	public		static	$settings	=	[];
	
	public	function __construct()
	{
		if(!is_object(self::$singleton))
			self::$singleton	=	$this;

		return self::$singleton;
	}
	
	public	function setDataAttr($type,$value)
	{
		if(is_object(self::$settings))
			self::$settings	=	$this->toArray(self::$settings);
		
		self::$settings[$type]	=	$value;
		
		self::$settings	=	$this->toObject(self::$settings);
		return $this;
	}
	
	public	function getDataAttr($type=false)
	{
		if($type)
			return (isset(self::$settings->{$type}))? self::$settings->{$type} : false;
		
		return self::$settings;
	}
	
	public	function toArray($item)
	{
		if(!is_array($item) && !is_object($item))
			return $item;
		
		if(is_object($item))
			return json_decode(json_encode($item),true);
		else
			return $item;
	}
	
	public	function toObject($item)
	{
		if(!is_array($item) && !is_object($item))
			return $item;
		
		if(is_array($item))
			return json_encode($item);
		else
			return $item;
	}
	
}