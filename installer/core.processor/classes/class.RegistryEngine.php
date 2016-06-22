<?php
/*
** @param $singleton - Saves RegisterSettings class to singleton
*/
	class	RegistryEngine
		{
			private	static	$singleton;
			
			public	static	function app()
				{
					if(!class_exists("RegisterSetting"))
						include_once(__DIR__."/class.RegisterSetting.php");
						
					return new RegisterSetting();
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
				//	print_r($use);
				//	print_r($data);
					
					switch($engine) {
							case ('errors'):
								self::app()->UseData($use, $data)->SaveTo("errors");
								break;
							case ('incidentals'):
								self::app()->UseData($use, $data)->SaveTo("incidentals");
								break;
							default:
								self::app()->UseData($use, $data)->SaveTo("settings");
						}
				}
		}