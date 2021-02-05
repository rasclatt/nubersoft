<?php
namespace Nubersoft;

class Widget extends \Nubersoft\Plugin
{
    private    $plugin,
            $widget,
            $slug,
            $author;
    private    static    $singleton;
    
    public function __construct($plugin)
    {
        if(empty(self::$singleton))
            self::$singleton    =    $this;
        $this->author    =
        $this->widget    =
        $this->slug        =    false;
        $this->plugin    =    $plugin;
        $this->init();
        
        return self::$singleton;
    }
    
    public function configPath()
    {
        return NBR_CLIENT_PLUGINS.DS.$this->plugin.DS.'config.xml';
    }
    
    public function init()
    {
        $plugin    =    $this->configPath();
        
        if(!is_file($plugin)) {
            trigger_error('Plugin does not exist.');
            return $this;
        }
        
        $this->widget    =    simplexml_load_file($plugin);
        $this->slug        =    $this->widget->widget->slug->__toString();
        $comp            =    $this->getOption($this->slug);
        
        return $this;
    }
    
    public function getConfig()
    {
        return $this->widget->widget;
    }
    
    public function getActions()
    {
        return $this->getConfig()->actions;
    }
    
    public function getBlockflows()
    {
        return $this->getConfig()->blockflows;
    }
    
    public function getSlug()
    {
        return $this->getConfig()->slug->__toString();
    }
    
    public function getName()
    {
        return $this->getConfig()->name->__toString();
    }
    
    public function getAuthor($key = false)
    {
        if(empty($this->author))
            $this->author    =    $this->toArray($this->getConfig()->author);
        $arr    =    $this->author;
        if(!empty($key))
            return (!empty($arr[$key]))? $arr[$key] : false;
        
        return $arr;
    }
    
    public function exists()
    {
        return is_file();
    }
    
    public function getAdminToolOptions()
    {
        return $this->toArray($this->getConfig()->admintools);
    }
    
    public function isActive()
    {
        $widget    =    $this->getOption('widget_'.$this->getSlug());
        return ($widget == 'on');
    }
    
    public function activate()
    {
        $this->setOption('widget_'.$this->getSlug(), 'on');
        $this->addComponent(['category_id' => 'widget_'.$this->getSlug(),'component_type' => 'receipt','content' => date('Y-m-d H:i:s')]);
    }
    
    public function deactivate()
    {
        $this->query("DELETE FROM main_menus WHERE parent_id = ?", ['widget_'.$this->getSlug()]);
        $this->deleteOption('widget_'.$this->getSlug());
        $this->deleteComponentBy(['category_id' => 'widget_'.$this->getSlug(),'component_type' => 'receipt']);
    }
}