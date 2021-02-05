<?php
namespace Nubersoft;

class nToken extends nSession
{
    public function setToken($name)
    {
        $this->set('token_'.$name, md5($name.date('Ymd').rand()));
        
        return $this;
    }
    
    public function getToken($name, $destory = true)
    {
        $token    =    $this->get('token_'.$name);

        if($destory)
            $this->destroy('token_'.$name);
        
        return $token;
    }
    
    public function removeToken($name)
    {
        $this->destroy('token_'.$name);
        
        return $this->tokenExists($name);
    }
    
    public function tokenExists($name)
    {
        return (!empty($this->get('token_'.$name)));
    }
    
    public function match($name, $hash, $destroy = false, $reset = false)
    {
        $match  =   ($this->getToken($name, $destroy) == $hash);
        if($reset) {
            $this->setToken($name);
        }
        
        return $match;
    }
} 