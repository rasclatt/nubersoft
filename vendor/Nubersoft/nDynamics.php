<?php
namespace Nubersoft;
/**
 *	@description	
 */
trait nDynamics
{
	/**
	 *	@description	
	 */
	public	function __call($method, $args)
	{
		$class	=	str_replace('_','\\', $method);
		$obj	=	nApp::call()->getHelper($class, ...$args);
		trigger_error('This class is being called dynamically, it may end up being slower doing it this way.', E_USER_NOTICE);
		return $obj;
	}
}