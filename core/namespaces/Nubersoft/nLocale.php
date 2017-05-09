<?php
namespace Nubersoft;

class nLocale extends \Nubersoft\nApp
	{
		function getTimezone($default = 'America/Los_Angeles')
			{
				if(!empty($this->getDataNode('timezone')))
					return $this->getDataNode('timezone');
				
				$prefs		=	$this->getSitePrefs();
				$timezone	=	(!empty($prefs->timezone))? $prefs->timezone : $default;
				$this->saveSetting('timezone',$timezone);
				return	$timezone;
			}
		
		function __toString()
			{
				return $this->getTimezone();
			}
	}
