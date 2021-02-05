<?php
namespace Nubersoft\nRender;

use \Nubersoft\ {
    nReflect as Reflect
};

trait enMasse
{
    public function getPage($key = false)
    {
        return Reflect::instantiate('\Nubersoft\nRender')->{__FUNCTION__}($key);
    }
}