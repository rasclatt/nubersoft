<?php
namespace Nubersoft\System;
/**
 *    @description    
 */
class User extends \Nubersoft\System
{
    /**
     *    @description    
     */
    public    function getUser($key = false)
    {
        $data    =    $this->get('user');
        
        if(!empty($key))
            return (!empty($data[$key]))? $data[$key] : null;
        
        return $data;
    }
}