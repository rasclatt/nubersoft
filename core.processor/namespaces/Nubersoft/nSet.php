<?php
namespace Nubersoft;

class nSet extends \RegistryEngine
	{
		public	static	function saveToPage($key,$value = false)
			{
				if(!isset(\NubeData::$settings->site))
					\NubeData::$settings->site	=	(object) array();
				
				\NubeData::$settings->site->{$key}	=	$value;
			}
	}