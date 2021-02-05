<?php
namespace Nubersoft\System\Component;
/**
 *    @description    
 */
class Observer extends \Nubersoft\System implements \Nubersoft\nObserver
{
    use \Nubersoft\Plugin\enMasse;
    /**
     *    @description    
     */
    public function listen()
    {
        $ID        =    (!empty($this->getPost("deliver")['ID']))? $this->getPost("deliver")['ID'] : false; 
        $comp    =    $this->getHelper('Settings')->getComponent($ID, 'ID', false);
        $this->ajaxResponse([
            'html' => [
                (!empty($ID))? $this->setPluginContent('component_content', $comp)->getPlugin('component') : $comp
            ],
            'sendto' => [
                '#editorid-'.$ID
            ]
        ]);
    }
}