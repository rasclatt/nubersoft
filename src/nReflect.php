<?php
namespace Nubersoft;

class nReflect
{
    public function execute()
    {
        $args    =    func_get_args();
        
        $class    =    $args[0];
        unset($args[0]);
        $Class    =    new \ReflectionClass($class);

        if(!$Class->isInstantiable()) {
             throw new \Exception("App failure. {$class} not found.".printpre());
         }
        
        $constr    =    $Class->getConstructor();
        
         if(!$constr) {
            if($hasDependencies = (count($args) > 0))
                unset($args);
            
             return ($hasDependencies)? new $class(...$args) : new $class();
        }
        
         $params        =    $constr->getParameters();
         $injects    =    $this->getParams($params,array_values($args));
        
         return $Class->newInstanceArgs($injects);
    }
    
    public function getParams($params,$args)
    {
        foreach($params as $key => $param) {
            $dependency        =    $param->getClass();
            
            if(!empty($dependency->name)) {
                $injectors[]    =    $this->execute($dependency->name);
            }
            else {
                if(class_exists('\Nubersoft\\'.$param->name))
                    $injectors[]    =    $this->execute('\Nubersoft\\'.$param->name);
                else {
                    $arg            =    (isset($args[$key]))? $args[$key] : false;
                    $injectors[]    =    ($param->isCallable())? $this->reflectFunction($param) : $arg;
                }
            }
        }
        
        return (!empty($injectors))? $injectors : [];
    }
    
    public    static    function instantiate()
    {
        $Class    =    new nReflect();
        return $Class->execute(...func_get_args());
    }
    
    public function reflectClassMethod($func,$method)
    {
        # Create Class
        $Class        =    (is_string($func))? $this->execute($func) : $func;
        # If there is no method to insert, just send class back
        if(empty($method))
            return $Class;
        # Create a reflector
        $Reflector    =    new \ReflectionClass($Class);
        # Check if the method exists
        $hasMethod    =    $Reflector->getMethod($method);
        # If exits
        if($hasMethod) {
            # Get the parameters
            $params        =    $hasMethod->getParameters();
            # If there are parameters requested
            if($params) {
                # Loop and assign as arguments
                foreach($params as $param) {
                    $rDep = $param->getClass();
                    $auto_injectors[] = (!empty($rDep->name))? $this->reflectClassMethod($rDep->name,false) : $param->getDefaultValue();
                }
            }
        }
        # Send back the injected class with injected classes into methods
        return (!empty($auto_injectors))? $Class->{$method}(...$auto_injectors) : $Class->{$method}();
    }
    /**
    *    @description    Creates auto-injected functions
    */
    public function reflectFunction($func)
    {
        $Reflector    =    new \ReflectionFunction($func);
        $params        =    $Reflector->getParameters();
        
        foreach($params as $parameter) {
            $class = $parameter->getClass();
            $auto_injectors[] = (!empty($class->name))? $this->reflectClassMethod($class->name,false) : $parameter->getDefaultValue();
        }
        
        # Send back the injected class with injected classes into methods
        return (!empty($auto_injectors))? $func(...$auto_injectors) : $func();
    }
}