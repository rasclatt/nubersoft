<?php
namespace Nubersoft;

class nSet extends \Nubersoft\RegistryEngine
{
	public	static	function saveToPage($key,$value = false)
	{
		if(!isset(parent::$settings->site))
			parent::$settings->site	=	(object) array();

		parent::$settings->site->{$key}	=	$value;
	}
}