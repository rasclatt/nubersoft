<?php
namespace Nubersoft;

class Conversion extends nApp
{
    public static function columnToTitle($title,$uc = false)
    {
        $title    = str_replace('_', ' ', $title);
        return ($uc)? ucwords($title) : $title;
    }
	/**
	 *	@description	
	 */
	public static function toString($element)
	{
        return StringWorks::toString($element);
	}
	/**
	 *	@description	
	 */
	public static function toXml(array $array)
	{
        return ArrayWorks::toXml($array);
	}
}