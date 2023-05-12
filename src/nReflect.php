<?php
namespace Nubersoft;

class nReflect
{
    # These are types that may have a default value
    private array $returnDefaultValues = [ 'int', 'dir', 'array', 'string', 'float'];

    public function execute()
    {
        $args = func_get_args();
        $class = $args[0];
        unset($args[0]);
        $Class = new \ReflectionClass($class);
        # See if this is going to work
        if (!$Class->isInstantiable()) {
            throw new \Exception("App failure. {$class} not found." . printpre());
        }
        # Check if this class is inheriting from another
        $constr = $Class->getConstructor();
        # If there are no inherited classes, just execute the class
        if (!$constr) {
            if ($hasDependencies = (count($args) > 0))
                unset($args);
            # Send back the class
            return ($hasDependencies) ? new $class(...$args) : new $class();
        }
        # Fetch from the inherited class
        $params = $constr->getParameters();
        # Fetch the parameters from the inherited class
        $injects = $this->getParams($params, array_values($args));
        # Recurse build the parent
        return $Class->newInstanceArgs($injects);
    }

    public function getParams($params, $args): array
    {
        foreach ($params as $key => $param) {
            # Get the type of parameter
            $dependency = @$param->getType();
            # See if it's typed
            $dependencyParam = ($dependency instanceof \ReflectionNamedType)? $dependency->getName() : null;
            # If the type is set and in the allowable list
            if(!empty($dependencyParam) && in_array($dependencyParam, $this->returnDefaultValues)) {
                # Get it's value or get the default value
                $injectors[] = ($args[$key])?? $param->getDefaultValue();
            }
            # If not in the allowed type list, then execute again
            else if (!empty($dependencyParam)) {
                $injectors[] = $this->execute($dependencyParam);
            } else {
                # See if this is a default nubersoft class
                if (class_exists('\Nubersoft\\' . $dependencyParam))
                    $injectors[] = $this->execute('\Nubersoft\\' . $dependencyParam);
                else {
                    # Last thing to try is get it's default value
                    $arg = (isset($args[$key])) ? $args[$key] : $param->getDefaultValue();
                    # If callable, set that
                    $injectors[] = ($dependencyParam == 'callable') ? $this->reflectFunction($param) : $arg;
                }
            }
        }

        return (!empty($injectors)) ? $injectors : [];
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
                # Loop and assign as arguments
                foreach ($params as $param) {
                    $rDep = $param->getClass();
                    $auto_injectors[] = (!empty($rDep->name)) ? $this->reflectClassMethod($rDep->name, false) : $param->getDefaultValue();
                }
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

        foreach ($params as $parameter) {
            $className = @$parameter->getType()->getName();
            //$class = $parameter->getClass();
            $auto_injectors[] = (!empty($className)) ? $this->reflectClassMethod($className, false) : $parameter->getDefaultValue();
        }

        # Send back the injected class with injected classes into methods
        return (!empty($auto_injectors)) ? $func(...$auto_injectors) : $func();
    }
}
