<?php
namespace Nubersoft\nRouter;

trait enMasse
{
    public function isAjaxRequest()
    {
        return \Nubersoft\nReflect::instantiate('\Nubersoft\nRouter\Controller')->{__FUNCTION__}();
    }
    
    public function ajaxResponse($arg, $modal = false)
    {
        return \Nubersoft\nReflect::instantiate('\Nubersoft\nRouter\Controller')->{__FUNCTION__}($arg, $modal);
    }
    
    public function getPage($arg)
    {
        return \Nubersoft\nReflect::instantiate('\Nubersoft\nRouter\Controller')->{__FUNCTION__}($arg);
    }
    
    public function redirect(string $arg)
    {
        return \Nubersoft\nReflect::instantiate('\Nubersoft\nRouter\Controller')->{__FUNCTION__}($arg);
    }
}