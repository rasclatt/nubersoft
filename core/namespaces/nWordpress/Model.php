<?php
namespace nWordpress;

class Model extends \Nubersoft\nApp
{
	/**
	*	@description	Converts camel case methods to function-based wordpress names and calls those functions
	*/
	public	static function __callStatic($class,$args=false)
	{
		# Converts the method name to wordpress functional
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $class, $matches);
		# Process name
		$ret	=	$matches[0];
		foreach($ret as &$match)
			$match	=	$match	==	(strtoupper($match))? strtolower($match) : lcfirst($match);
		# Strings the match together to turn something like "thisIsMyFunction" to "this_is_my_function"
		$func	=	implode('_', $ret);
		# Creates an output buffer and returns the contents
		return (new Cache())->capture(function() use ($func,$args){
			echo (!empty($args))? $func(...$args) : $func();
		})->get();
	}
}