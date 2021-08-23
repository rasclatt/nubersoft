<?php
namespace Nubersoft\Plugin;

use \Nubersoft\ {
    nReflect as Reflect,
    Plugin
};

use \Nubersoft\Dto\Settings\Page\View\ConstructRequest;

trait enMasse
{
    private    $pluginObj;
    
    public function getPlugin(string $dir, string $path = null, bool $return = false)
    {
        $this->pluginObj = new Plugin(new ConstructRequest());
        return $this->pluginObj->{__FUNCTION__}($dir, $path, $return);
    }
    
    public function getPluginFrom(string $template, string $plugin, string $file = null)
    {
        $this->pluginObj = new Plugin(new ConstructRequest());
        return $this->pluginObj->{__FUNCTION__}($template, $plugin, $file);
    }
    
    public function getPluginInfo(string $name = null)
    {
        return $this->pluginObj->{__FUNCTION__}($name);
    }
    
    public function setPluginContent(string $name, $value)
    {
        return (new Plugin(new ConstructRequest()))->{__FUNCTION__}($name, $value);
    }
}