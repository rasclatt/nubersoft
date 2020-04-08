<?php
namespace Nubersoft\DataNode;

trait enMasse
{
    public    function removeNode($name, $subkey = false)
    {
        return (new \Nubersoft\DataNode())->{__FUNCTION__}($name, $subkey);
    }
    
    public    function setNode($node_name, $data = false)
    {
        return (new \Nubersoft\DataNode())->{__FUNCTION__}($node_name, $data);
    }
    
    public    function clearAll()
    {
        (new \Nubersoft\DataNode())->{__FUNCTION__}();
    }
    
    public    function addNode($node_name, $data = false, $key = false)
    {
        return (new \Nubersoft\DataNode())->{__FUNCTION__}($node_name, $data, $key);
    }
    
    public    function keyExists($array, $key = false)
    {
        return (new \Nubersoft\DataNode())->{__FUNCTION__}($array, $key);
    }
    
    public    function getDataNode($key = false)
    {
        return (new \Nubersoft\DataNode())->{__FUNCTION__}($key);
    }
}