<?php
namespace Nubersoft;

class nTranslator extends \Nubersoft\nApp
{
	private	static	$defLang		=	'en';
	private	static	$lang			=	'en';
	private	static	$phrase_match	=	true;
	
	public	static function setDefLang($lang)
	{
		self::$defLang	=	$lang;
		return new nTranslator();
	}
	
	public	static function setLang($lang)
	{
		self::$lang	=	$lang;
		return new nTranslator();
	}
	
	public function getStringEquivalent($string,$keymatch)
	{
		$type	=	(!empty(self::$phrase_match))? 'phrases' : 'words';
		$file	=	$this->getSystemFile('register'.DS.'languages'.DS.$type.DS.self::$lang.'.json');
		
		if(empty($file))
			return $string;
		
		$translations	=	$this->jsonFromFile($file);
		$stringKey		=	(!empty($keymatch))? $keymatch : strtolower($string);
		
		return (isset($translations[$stringKey]))? $translations[$stringKey] : $string;
	}
	
	public	static	function setType($phrase)
	{
		self::$phrase_match	=	$phrase;
	}
}