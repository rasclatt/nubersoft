<?php
namespace Nubersoft;

use \Nubersoft\ {
    nReflect,
    nMarkUp\enMasse as nMarkUp
};

class nAutomator extends nApp
{
    use nMarkUp;
    
    public function getClientWorkflow($type)
    {
        return $this->getWorkflowFile($type, NBR_CLIENT_WORKFLOWS);
    }
    
    public function getSystemWorkflow($type)
    {
        $workflow    =    $this->getWorkflowFile($type, NBR_WORKFLOWS);
        
        if(empty($workflow))
            throw new HttpException('System Workflow ('.$this->enc($type).') is missing or invalid.'.printpre(false), 100);
        
        return $workflow;
    }
    
    public    function getClientBlockflow($type)
    {
        return $this->getWorkflowFile($type, NBR_CLIENT_BLOCKFLOWS);
    }
    
    public    function getSystemBlockflow($type)
    {
        $workflow    =    $this->getWorkflowFile($type, NBR_BLOCKFLOWS);
        
        if(empty($workflow))
            throw new HttpException('System Blockflow ('.$this->enc($type).') is missing or invalid.'.printpre(false), 100);
        
        return $workflow;
    }
    
    public function getWorkflowFile($type, $from)
    {
        return $this->getHelper('Conversion\Data')->xmlToArray($from.DS.$type.'.xml');
    }
    /**
     *    @description    Recursively instanciates classes and processes injectiables
     *    @param    $array [array]    This is the recursable array of attributes which determine what class to run
     *                            and all the dependencies
     */
    public function doClassWorkflow($array)
    {
        # Set out names for the class and method
        $class    =    $array['name'];
        $method    =    ($array['method'])?? null;
        if(!$method)
            throw new \Exception('You must have a method in your workflow', 500);
        # See if there is a non-specified injector (no into="method")
        if(!empty($array['inject'][$method])) {
            $args    =    $this->doInjection($array['inject'][$method]);
        }
        # Check if there is a construct injection
        # Specify construct if the injectable requires a parameter but parent method doesn't allow for injection
        if(!empty($args) || !empty($array['inject']['__construct'])) {
            # Fetch the injectables
            $constr    =    (!empty($array['inject']['__construct']))? $this->doInjection($array['inject']['__construct']) : null;
            # Create new object, inject construct params if there
            $Obj    =    (!empty($constr))? new $class(...$constr) : nReflect::instantiate($class);
            # We don't want to call construct twice, so just run if not construct
            if($method != '__construct') {
                # If there are arguments, inject those
                if(!empty($args))
                    $Obj->{$method}(...$args);
                else
                    $Obj->{$method}();
            }
        }
        # Create a reflection for auto-injection
        else {
            $Reflect    =    new nReflect();
            $Obj        =    $Reflect->reflectClassMethod($array['name'], $array['method']);
        }
        # Check for any methods that need to be chained
        if(!empty($array['chain'])) {
            # Create an array if only one chain
            if(is_string($array['chain'])) {
               $array['chain']    =    [
                   $array['chain']
               ];
            }
            # Loop the chainables
            foreach($array['chain'] as $chain) {
                # Check for injections into the chained methods
                $inj    =    ((!empty($array['inject'][$chain]))? $this->doInjection($array['inject'][$chain]): null);
                # Chain the objects together. Requires that methods are public and return "$this"
                $Obj    =    (is_array($inj))? $Obj->{$chain}(...$inj) : $Obj->{$chain}($inj);
            }
        }
        # Return the final object for use
        return $Obj;
    }
    /**
     *    @description    Determines if there are things to inject
     *    @param    $array    [array|bool] This will be an array of nested arrays/values to process
     *    @returns    Can return an array of mixed types
     */
    public    function doInjection($array)
    {
        $storage    =    [];
        # Take the injectable and see if a class exists to build
        if(isset($array['object'])) {
            foreach($array['object'] as $event => $object) {
                if(isset($object['class'])) {
                    foreach($object['class'] as $class) {
                        $storage[]    =    $this->doClassWorkflow($class);
                    }
                }
            }
        }
        # See if there is an array of values to inject
        elseif(isset($array['array'])) {
            if(isset($array['array']['arg'])) {
                if(!isset($array['array']['arg'][0]))
                    $array['array']['arg']    =    [$array['array']['arg']];
                
                $storage[]    =    $array['array']['arg'];
            }
        }
        # See if we are injecting a string
        elseif(isset($array['string'])) {
            if(!isset($array['string'][0]))
                $array['string']    =    [$array['string']];

            $storage[]    =    $array['string'];
        }
        # Send back the items to inject into the parent
        return $storage;
    }
    /**
     *    @description    Base method to loop the workflow objects
     */
    public    function doWorkflow($array)
    {
        //if(!isset($array['object'][0]))
        //    $array['object']    =   [$array['object']];
        
        foreach($array['object'] as $event => $object) {
            if(isset($object['class'])) {
                foreach($object['class'] as $classObj) {
                    $this->doClassWorkflow($classObj);
                }
            }
        }
    }
    /**
     *    @description    This takes an array and tries to make all the nested values the same type so it's easily recursed
     *    @param    $array [array]
     */
    public    function normalizeWorkflowArray($array)
    {
        if(!is_array($array))
            return $array;
        elseif(!isset($array['object']))
            return $array;
        
        if(!isset($array['object'][0])) {
            $array['object']    =    [
                $array['object']
            ];
        }
        
        $new    =    [];
        
        foreach($array['object'] as $key => $object) {
            $nameAttr    =    $object['@attributes']['event'];
            unset($object['@attributes']['event']);
            if(empty($object['@attributes']))
                unset($object['@attributes']);
            $new['object'][$nameAttr]    =    $object;
            if(isset($array['object'][$key]['class'])) {
                if(!isset($array['object'][$key]['class'][0])){
                    $new['object'][$nameAttr]['class']    =
                    $array['object'][$key]['class']    =    [$object['class']];
                }
                
                foreach($array['object'][$key]['class'] as $skey => $class) {
                    $new['object'][$nameAttr]['class'][$skey]    =    $class;
                    if(isset($array['object'][$key]['class'][$skey]['inject'])) {
                        $new['object'][$nameAttr]['class'][$skey]['inject']    =
                        $array['object'][$key]['class'][$skey]['inject']    =    $this->setInjectName($class['inject']);
                        if(!empty($class['inject']['@attributes']))
                            $new['object'][$nameAttr]['class'][$skey]['inject']['@attributes']    =    $class['inject']['@attributes'];
                    }
                    else
                        $new['object'][$nameAttr]['class'][$skey]    =    $class;
                }
            }
            elseif(isset($array['object'][$key]['include'])) {
                
                if(!is_array($array['object'][$key]['include'])) {
                    $array['object'][$key]['include']    =    [$array['object'][$key]['include']];
                }
                
                foreach($array['object'][$key]['include'] as $incl) {
                    $incl    =    $this->useMarkUp($incl);
                    if(is_file($incl))
                        echo $this->render($incl, new nRender());
                }
            }
        }
        
        return $new;
    }
    /**
     *    @description    Processes an array and makes ture to pull out the name of the method to inject into
     */
    protected    function setInjectName($array)
    {
        if(!is_array($array))
            return $array;
        
        if(!isset($array[0])) {
            $array    =    [
                $array
            ];
        }
        $new    =    [];
        foreach($array as $injector) {
            $into    =    $injector['@attributes']['into'];
            unset($injector['@attributes']['into']);
            if(empty($injector['@attributes']))
                unset($injector['@attributes']);
            
            $new[$into]    =    $this->normalizeWorkflowArray($injector);
        }
        
        return $new;
    }
}