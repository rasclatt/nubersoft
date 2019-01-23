<?php
namespace Nubersoft\Settings\Page;
/**
 *	@description	
 */
trait enMasse
{
	public	function siteLogoActive()
	{
		return $this->getHelper('Settings\Page\Controller')->{__FUNCTION__}();
	}
	
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
		try {
			if(method_exists($class, $method))
				return (new $class)->$method(...$args);
			else
				return $class->{$method}(...$args);
		}
		catch (\Exception $e) {
			trigger_error('Class/Method does not exist: '.$class.'::'.$method);
		}
	}
}