<?php
namespace Nubersoft;

class nGlobal extends \Nubersoft\nApp
{
	private static	$enttable;
	
	public	function sanitize($value)
	{
		if(!is_array($value) && !is_object($value)) {
			if(!is_numeric($value) && !is_bool($value) && !is_int($value) && !is_float($value)) {
				return (string) htmlentities(trim($value), ENT_QUOTES, 'UTF-8');
			}
			else
				return trim($value);
		}
		
		if(is_object($value))
			$value	=	$this->toArray($value);
		
		foreach($value as $key => $subval) {
			$value[$key]	=	$this->sanitize($subval);
		}
		
		ksort($value);
		
		return $value;
	}
}