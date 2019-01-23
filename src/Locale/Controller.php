<?php
namespace Nubersoft\Locale;

use \Nubersoft\ArrayWorks;
/**
 *	@description	
 */
class Controller extends \Nubersoft\Locale
{
	/**
	 *	@description	
	 */
	public	function getCountries($abbrCnt = 3)
	{
		return array_keys(ArrayWorks::organizeByKey($this->getLocaleData($abbrCnt)->getData(), 'title'));
	}
}