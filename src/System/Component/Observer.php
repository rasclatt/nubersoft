<?php
namespace Nubersoft\System\Component;

use \Nubersoft\ {
    System,
    nObserver
};
/**
 *    @description    
 */
class Observer extends System implements nObserver
{
    use \Nubersoft\Plugin\enMasse;
    /**
     *    @description    
     */
    public function listen()
    {
        $ID        =    (!empty($this->nApp->getPost("deliver")['ID']))? $this->nApp->getPost("deliver")['ID'] : false; 
        $comp    =    (new \Nubersoft\Settings)->getComponent($ID, 'ID', false);
        $this->nApp->ajaxResponse([
            'html' => [
                (!empty($ID))? $this->setPluginContent('component_content', $comp)->getPlugin('component') : $comp
            ],
            'sendto' => [
                '#editorid-'.$ID
            ]
        ]);
    }
}