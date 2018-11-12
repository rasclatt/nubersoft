<?php
namespace Nubersoft\System;
/**
 *	@description	
 */
trait enMasse
{
	/**
	 *	@description	
	 */
	public	function getThumbnail($pathname, $imagename)
	{
		return (new \Nubersoft\System\Controller())->{__FUNCTION__}($pathname, $imagename);
	}
}