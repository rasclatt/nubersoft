<?php
namespace Nubersoft\nMarkUp;

use \Nubersoft\ {
    nMarkUp as MarkUp,
    nReflect as Reflect
};

trait enMasse
{
    public function useMarkUp()
    {
        return Reflect::instantiate('\Nubersoft\nMarkUp')->useMarkUp(...func_get_args());
    }
}