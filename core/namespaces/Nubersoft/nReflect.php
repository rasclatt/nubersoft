<?php
namespace Nubersoft;

class nReflect
{
	private	static	$singleton;
	
	public	function __construct()
	{
		if(self::$singleton instanceof \Nubersoft\nReflect)
			return self::$singleton;
		
		return  self::$singleton	=	$this;
	}
	
	public	function execute()
	{
		$args	=	func_get_args();
		
		$class	=	$args[0];
		unset($args[0]);
		$Class	=	new \ReflectionClass($class);

		if(!$Class->isInstantiable()) {
 			throw new nException("App failure. {$class} not found.");
 		}
		
		$constr	=	$Class->getConstructor();
		
 		if(!$constr) {
			if($hasDependencies = (count($args) > 0))
				unset($args);
			
 			return ($hasDependencies)? new $class(...$args) : new $class();
		}
		
 		$params		=	$constr->getParameters();
 		$injects	=	$this->getParams($params);
		
 		return $Class->newInstanceArgs($injects);
	}
	
	public function getParams($params)
	{
		foreach($params as $param) {
			$dependency		=	$param->getClass();
			if(!empty($dependency->name))
				$injectors[]	=	$this->execute($dependency->name);
		}
		
		return (!empty($injectors))? $injectors : [];
	}
	
	public	static	function instantiate()
	{
		$Class	=	new nReflect();
		return $Class->execute(...func_get_args());
	}
	
	public	function reflectClassMethod($func,$method)
	{
		# Create Class
		$Class		=	(is_string($func))? $this->execute($func) : $func;
		# If there is no method to insert, just send class back
		if(empty($method))
			return $Class;
		# Create a reflector
		$Reflector	=	new \ReflectionClass($Class);
		# Check if the method exists
		$hasMethod	=	$Reflector->getMethod($method);
		# If exits
		if($hasMethod) {
			# Get the parameters
			$params		=	$hasMethod->getParameters();
			# If there are parameters requested
			if($params) {
				# Loop and assign as arguments
				foreach($params as $param) {
					$rDep	=	$param->getClass();
					$auto_injectors[]	=	(!empty($rDep->name))? $this->reflectClassMethod($rDep->name,false) : $param;
				}
			}
		}
		# Send back the injected class with injected classes into methods
		return (!empty($auto_injectors))? $Class->{$method}(...$auto_injectors) : $Class->{$method}();
	}
}