<?php
namespace Nubersoft;

class DataNode extends \Nubersoft\nApp
{
    private    static    $data    =    [];
    
    public function setNode($node_name, $data = false)
    {
        self::$data[$node_name]    =    $data;
        
        return $this;
    }
    
    public function removeNode($node_name, $subkey = false)
    {
        if(isset(self::$data[$node_name])) {
            if($subkey) {
                if(isset(self::$data[$node_name][$subkey]))
                    unset(self::$data[$node_name][$subkey]);
            }
            else {
                unset(self::$data[$node_name]);
            }
        }
        
        return (isset(self::$data[$node_name]));
    }
    
    public function clearAll()
    {
        self::$data    =    [];
    }
    
    public function addNode($node_name, $data = false, $key = false)
    {
        if(isset(self::$data[$node_name])) {
            if(!is_array(self::$data[$node_name])) {
                self::$data[$node_name]    =    [
                    self::$data[$node_name],
                    $data
                ];
            }
            else {
                if($key)
                    self::$data[$node_name][$key]    =    $data;
                else
                    self::$data[$node_name][]    =    $data;
            }
        }
        else
            $this->setNode($node_name, $data);
        
        if(is_array(self::$data[$node_name]))
            ksort(self::$data[$node_name]);
    }
    
    public function keyExists($array, $key = false)
    {
        if(isset(self::$data[$array])) {
            if(!empty($key)) {
                return (isset(self::$data[$array][$key]));
                
                return true;
            }
            
            return false;
        }
    }
    
    public function getDataNode($key = false)
    {
        if($key)
            return (isset(self::$data[$key]))? self::$data[$key] : null;
            
        return self::$data;
    }
}