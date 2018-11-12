<?php
namespace Nubersoft\Settings\Page;
/**
 *	@description	
 */
trait enMasse
{
	public	function getSiteLogo($alt = false, $html = true)
	{
		$path	=	$this->getHelper('Settings\Page\Controller')->{__FUNCTION__}();
		
		if(empty($path)) {
			if(empty($alt))
				return false;
			else {
				$base	=	$this->localeUrl($alt);
				return ($html)? '<img src="'.$base.'" class="site-image" />' : $base;
			}
		}
		
		return ($html)? '<img src="'.$path.'" class="site-image" />' : $path;
	}
	
	public	function __call($method, $args)
	{
		$args	=	(!is_array($args))? [] : $args;
		$class	=	$this->getHelper('Settings\Page\Controller');
		if(!method_exists($class, $method))
			return (new \Nubersoft\nDynamics)->__call($method, ...$args);
		else
			return $class->{$method}(...$args);
	}
}