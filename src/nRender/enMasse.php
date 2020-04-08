<?php
namespace Nubersoft\nRender;

trait enMasse
{
    public    function getPage($key = false)
    {
        return (new \Nubersoft\nRender())->{__FUNCTION__}($key);
    }
}