<?php
namespace Nubersoft\API;

class Model extends \Nubersoft\API\Core
{
    public function sendAsPost($args=false,$decode=false)
    {
        $this->remote($args,$decode,true);
        return $this;
    }
    
    public function sendAsGet($args=false,$decode=false)
    {
        $this->remote($args,$decode,false);
        return $this;
    }
    
    public function getResponse($decode=false)
    {
        switch(strtolower($decode)){
            case('json'):
                return json_decode(parent::getResponse(),true);
            case('xml'):
                return simplexml_load_string(parent::getResponse());
            default:
                return parent::getResponse();
        }
    }
}