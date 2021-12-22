<?php
namespace Nubersoft\System;

use \Nubersoft\ {
    System\Controller,
    nApp,
    nQuery
};
/**
 *    @description    
 */
trait enMasse
{
    /**
     *    @description    
     */
    public function getThumbnail($pathname, $imagename)
    {
        return (new Controller(new nApp, new nQuery))->{__FUNCTION__}($pathname, $imagename);
    }
}