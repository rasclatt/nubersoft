<?php
namespace Nubersoft;
/**
 *	@description	
 */
class StringWorks extends nApp
{
	/**
	 *	@description	
	 */
	public static function braceReplace(string $string, array $array): string
	{
        $keys   =   array_keys($array);
        return preg_replace_callback('/{{'.implode('}}|{{', $keys).'}}/', function($v) use ($array){
            return ($array[rtrim(ltrim($v[0], '{{'), '}}')])?? false;
        }, $string);
	}
	/**
	 *	@description	
	 */
	public static function toString($element):? string
	{
        if(is_array($element) || is_object($element))
            return json_encode($element);
        elseif(is_bool($element))
            return ($element)? "true" : "false";
        else
            return "{$element}";
	}
	/**
	 *	@description	
	 */
	public function toXml($xml):? string
	{
        return '';
	}
}