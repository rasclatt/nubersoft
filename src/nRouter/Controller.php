<?php
namespace Nubersoft\nRouter;

class Controller extends \Nubersoft\nRouter
{
    public    function setHeader()
    {
        $args    =    func_get_args();
        $exit    =    (isset($args[1]))? $args[1] : false;
        if(!is_array($args[0]))
            $args[0]    =    [$args[0]];
        
        foreach($args[0] as $header) {
            header($header);
        }
        
        if($exit)
            exit;
        
        return $this;
    }
    
    public    function redirect($location)
    {
        $arr    =    parse_url($location);
                
        if(!empty($arr['query'])) {
            parse_str($arr['query'], $arr['query']);
            if(isset($arr['query']['msg'])) {
                $arr['query']['msg']    =    urlencode($this->getHelper('nCrypt')->encOpenSSL($arr['query']['msg']));

                $arr['query']    =    '?'.http_build_query($arr['query']);
                $arr['scheme']    .=    ':';
        
                $location    =    implode('',$arr);
            }
        }
        
        $this->setHeader('Location: '.$location, true);
    }
    
    public    function isAjaxRequest()
    {
        $type    =    $this->getDataNode('request');
        
        if(!empty($type))
            return ($type == 'ajax');
        else
            return (strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
    }
    
    public    function ajaxResponse($item, $modal = false)
    {
        if($modal) {
            if(!empty($item['html'][0])) {
                $item['html'][0]    =    \Nubersoft\nApp::createContainer(function(\Nubersoft\Plugin $Plugin) use ($item) {
                    return $Plugin->setPluginContent('modal', [
                        'html' => $item['html'][0],
                        'title' => (!empty($item['title']))? $item['title'] : ""
                    ])->getPlugin('modal_window');
                });
                
                die(json_encode($item));
            }
        }
        else {
            if(is_array($item) || is_object($item))
                die(json_encode($item));
            else
                die($item);
        }
    }
}