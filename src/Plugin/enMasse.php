<?php
namespace Nubersoft\Plugin;

trait enMasse
{
    private    $pluginObj;
    
    public    function getPlugin($dir, $path = false, $return = false)
    {
        $this->pluginObj    =    new \Nubersoft\Plugin();
        return $this->pluginObj->{__FUNCTION__}($dir, $path, $return);
    }
    
    public    function getPluginInfo($name = false)
    {
        return $this->pluginObj->{__FUNCTION__}($name);
    }
    
    public    function setPluginContent($name, $value)
    {
        return (new \Nubersoft\Plugin())->{__FUNCTION__}($name, $value);
    }
}