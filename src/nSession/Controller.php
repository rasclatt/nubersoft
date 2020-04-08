<?php
namespace Nubersoft\nSession;

class Controller extends \Nubersoft\nSession
{
    public    function setLastActive()
    {
        $timeout    =    (defined('LAST_ACTIVE'))? LAST_ACTIVE : 3500;
        $this->set('LAST_ACTIVE', strtotime('now')+$timeout);
        return $this;
    }
}