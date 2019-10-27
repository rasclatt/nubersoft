<?php
namespace Nubersoft;
/**
 *	@description	
 */
class Money extends \Nubersoft\nApp
{
	public	static	function toDollar($value, $lang_CO = 'en_US')
	{
		setlocale(LC_MONETARY, $lang_CO.'.UTF-8');
		return money_format('%.2n', $value);
	}
	/**
	 *	@description	
	 */
	public	static	function getLocaleAbbr($abbr, $lang = 'en')
	{
		$data	=	self::getLocaleList();
		$key	=	implode('_', array_filter([$lang,$abbr]));
		return (isset($data[$key]))? $key : 'en_US';
	}
	/**
	 *	@description	
	 */
	public	static	function getLocaleList($country = false)
	{
		$data	=	json_decode(file_get_contents(NBR_SETTINGS.DS.'locale'.DS.'locales.json'), 1);
		
		if($country) {
			$new	=	[];
			foreach($data as $locale => $name) {
				if(stripos($name, $country) !== false)
					$new[$locale]	=	$name;
			}
			return $new;
		}
		return $data;
	}
	/**
	 *	@description	
	 */
	public	static	function toCurrency($value, $country = 'US', $lang = 'en')
	{
		return self::toDollar($value, self::getLocaleAbbr($country, $lang));
	}
}