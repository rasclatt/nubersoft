<?php
namespace Nubersoft;
/*
** @param $singleton - Saves RegisterSettings class to singleton
*/
class	RegistryEngine extends \Nubersoft\Singleton
{
	public	static	function app()
	{
		return nApp::call('RegisterSetting');
	}

	private	static	function getArgs($args)
	{
		if(empty($args))
			return false;

		$use	=	(isset($args[0]) && is_string($args[0]))? $args[0] : false;
		$data	=	(isset($args[1]))? $args[1] : 'NBR::EMPTY';

		return (empty($use))?  array("use"=>false,"data"=>false) : array("use"=>$use,"data"=>$data);
	}

	public	static	function saveSetting()
	{
		// Fetch arguments
		$args	=	self::getArgs(func_get_args());
		self::init('settings',$args['use'], $args['data']);
	}

	public	static	function saveError()
	{
		// Fetch arguments
		$args	=	self::getArgs(func_get_args());
		self::init('errors',$args['use'], $args['data']);
	}

	public	static	function saveIncidental()
	{
		// Fetch arguments
		$args	=	self::getArgs(func_get_args());
		self::init('incidentals',$args['use'], $args['data']);
	}

	private	static	function init($engine = 'settings',$use,$data)
	{
		switch($engine) {
			case ('errors'):
				self::app()->useData($use, $data)->saveTo("errors");
				break;
			case ('incidentals'):
				self::app()->useData($use, $data)->saveTo("incidentals");
				break;
			default:
				self::app()->useData($use, $data)->saveTo("settings");
		}
	}
}