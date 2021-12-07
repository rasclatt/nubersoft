<?php
namespace Nubersoft;
/**
 *	@description	
 */
interface JWTI
{
    public function encode($body);
    
    public function decode($token);
    
    public function setKey($key);
    
    public function getKey();
    
    public function setAlgo($algo, $reset = false);
}