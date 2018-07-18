<?php
namespace nWordpress;

class Controller extends \nWordpress\Model
{
	protected	static	$wp_settings;
	
	public	function routerCount()
	{
		$routers	=	$this->getRoutes();
		
		return count($routers);
	}
	
	public	function routingActive()
	{
		return (get_option('nbr_activate_router',false) == 'on');
	}
	
	public	function getRoutes($refresh=false)
	{
		$routes	=	json_decode(get_option('nbr_activate_routers','[]'),true);
		if(!empty($routes['from'])) {
			$routing	=	[];
			foreach($routes['from'] as $key => $value) {
				$routing[$key]['from']		=	$value;
				$routing[$key]['to']		=	(!empty($routes['to'][$key]))? $routes['to'][$key] : false;
				$routing[$key]['title']		=	(!empty($routes['title'][$key]))? $routes['title'][$key] : false;
				$routing[$key]['parent']	=	(!empty($routes['parent'][$key]))? $routes['parent'][$key] : false;
				$routing[$key]['force']		=	(!empty($routes['force'][$key]))? $routes['force'][$key] : false;
				$routing[$key]['loggedin']	=	(!empty($routes['loggedin'][$key]))? $routes['loggedin'][$key] : 'both';

			}
		}

		return (!empty($routing))? $routing : $routes;
	}
	/**
	*	@description	Converts camel case methods to function-based wordpress names and calls those functions
	*/
	public	function __call($class,$args=false)
	{
		# Converts the method name to wordpress functional
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $class, $matches);
		# Process name
		$ret	=	$matches[0];
		foreach($ret as &$match)
			$match	=	$match	==	(strtoupper($match))? strtolower($match) : lcfirst($match);
		# Strings the match together to turn something like "thisIsMyFunction" to "this_is_my_function"
		$func	=	implode('_', $ret);
		# Creates an output buffer and returns the contents
		$render	=	(!empty($args))? $func(...$args) : $func();
		return $render;
	}
}