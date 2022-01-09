<?php
namespace Nubersoft;

class nReflect
{
    public function execute()
    {
        $args = func_get_args();

        $class = $args[0];
        unset($args[0]);
        $Class = new \ReflectionClass($class);

        if (!$Class->isInstantiable()) {
            throw new \Exception("App failure. {$class} not found." . printpre());
        }

        $constr = $Class->getConstructor();

        if (!$constr) {
            if ($hasDependencies = (count($args) > 0))
                unset($args);

            return ($hasDependencies) ? new $class(...$args) : new $class();
        }
        # Fetch parameters
        $params = $constr->getParameters();
        # Generate auto injectors
        $injects = $this->getParams($params, array_values($args));
        # Create the object
        return $Class->newInstanceArgs($injects);
    }

    public function getParams($params, $args)
    {
        foreach ($params as $key => $param) {
            $dependency = $param->getType();

            if ($dependency == null) {
                $injectors[] = $dependency;
                continue;
            }
            $cname = $dependency->getName();
            if (class_exists($cname)) {
                $injectors[] = $this->execute($cname);
            } else {
                if (class_exists($cname = '\Nubersoft\\' . $cname))
                    $injectors[] = $this->execute($cname);
                else {
                    $arg = (isset($args[$key])) ? $args[$key] : false;
                    $injectors[] = ($this->isCallable($param)) ? $this->reflectFunction($param) : $arg;
                }
            }
        }

        return (!empty($injectors)) ? $injectors : [];
    }
    /**
     *	@description	
     *	@param	
     */
    protected function isCallable($param): bool
    {
        if (!$param)
            return false;

        $types = ($param instanceof \ReflectionUnionType) ? $param->getTypes() : [$param];

        return in_array('callable', array_map(function ($t) {
            return ($t instanceof \ReflectionNamedType) ? $t->getName() : $t;
        }, $types));
    }

    public static function instantiate()
    {
        $Class = new nReflect();
        return $Class->execute(...func_get_args());
    }

    public function reflectClassMethod($func, $method)
    {
        # Create Class
        $Class = (is_string($func)) ? $this->execute($func) : $func;
        # If there is no method to insert, just send class back
        if (empty($method))
            return $Class;
        # Create a reflector
        $Reflector = new \ReflectionClass($Class);
        # Check if the method exists
        $hasMethod = $Reflector->getMethod($method);
        # If exits
        if ($hasMethod) {
            # Get the parameters
            $params = $hasMethod->getParameters();
            # If there are parameters requested
            if ($params) {
                # Process parameters
                $auto_injectors = $this->autoInjectable($params);
            }
        }
        # Send back the injected class with injected classes into methods
        return (!empty($auto_injectors)) ? $Class->{$method}(...$auto_injectors) : $Class->{$method}();
    }
    /**
     * @description Creates auto-injected functions
     */
    public function reflectFunction($func)
    {
        $Reflector = new \ReflectionFunction($func);
        $params = $Reflector->getParameters();
        # Process parameters
        $auto_injectors = $this->autoInjectable($params);
        # Send back the injected class with injected classes into methods
        return (!empty($auto_injectors)) ? $func(...$auto_injectors) : $func();
    }
    /**
     *	@description	
     *	@param	
     */
    private function autoInjectable(array $params)
    {
        $auto_injectors = [];
        foreach ($params as $parameter) {
            $class = $parameter->getType();
            $isClass = class_exists($cname = $class->getName());
            $auto_injectors[] = ($isClass) ? $this->reflectClassMethod($cname, false) : $parameter->getDefaultValue();
        }
        # Return the parameters
        return $auto_injectors;
    }
}
