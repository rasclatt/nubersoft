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
	/**
	*	@description	Turns an object to array
	*/
	public	function toArray()
	{
		$args	=	func_get_args();
		$var	=	(!empty($args[0]))? $args[0] : false;

		if(empty($var))
			return $var;

		return (is_object($var) || is_array($var))? json_decode(json_encode($var),true) : $var;
	}
	/**
	*	@description	Turns an array to object
	*/
	public	function toObject()
	{
		$args	=	func_get_args();
		$var	=	(!empty($args[0]))? $args[0] : false;

		if(empty($var))
			return $var;

		return (is_object($var) || is_array($var))? json_decode(json_encode($var,JSON_FORCE_OBJECT)) : $var;
	}
	
	public	function __($string,$matchkey=false,$func=false,$phrase=true)
	{
		$nTranslator	=	new nTranslator();
		nTranslator::setType($phrase);
		$string	=	$nTranslator->getStringEquivalent($string,$matchkey);
		return (is_callable($func))? $func($string,$nTranslator) : $string;
	}
}