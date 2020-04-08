<?php
namespace Nubersoft\nMarkUp;

use \Nubersoft\nMarkUp as MarkUp;

trait enMasse
{
    public    function useMarkUp()
    {
        return (new \Nubersoft\nMarkUp())->useMarkUp(...func_get_args());
    }
}