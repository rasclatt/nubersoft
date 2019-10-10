<?php
namespace Nubersoft\nCookie;
/**
 *	@description	
 */
class Observer extends \Nubersoft\nCookie implements \Nubersoft\nObserver
{
	/**
	 *	@description	
	 */
	public	function listen()
	{
	}
	/**
	 *	@description	
	 */
	public	function setCurrentPage()
	{
		if($this->isAjaxRequest())
			return false;
		
		$args		=	func_get_args();
		$duration	=	3600;
		$path		=	'/';
		$domain		=	null;
		
		if(!empty($args[0]) && is_array($args[0])) {
			$duration	=	(isset($args[0][0]))? $args[0][0] : $duration;
			$path		=	(isset($args[0][1]))? $args[0][1] : $path;
			$domain		=	(isset($args[0][2]))? $args[0][2] : $domain;
			
		}
		else
			$duration	=	$args[0];
		
		$this->set('nbr_current_page', ['self' => $_SERVER['PHP_SELF'], 'request' => $_SERVER['REQUEST_URI'], 'referrer' => $_SERVER['HTTP_REFERER']], $duration, $path, $domain);
		return $this;
	}
}