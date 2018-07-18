<?php
namespace Nubersoft;

class Settings extends \Nubersoft\GetSitePrefs
{
	public	function setTimeZone($locale=false)
	{
		if(!$locale)
			$locale	=	$this->getHelper('nLocale')->getTimezone();

		date_default_timezone_set($locale);
	}
}