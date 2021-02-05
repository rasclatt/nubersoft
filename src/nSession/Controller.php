<?php
namespace Nubersoft\nSession;

class Controller extends \Nubersoft\nSession
{
    public function setLastActive()
    {
        $timeout    =    (defined('SESSION_EXPIRE_TIME'))? SESSION_EXPIRE_TIME : 3600;
        $this->set('LAST_ACTIVE', strtotime('now')+$timeout);
        return $this;
    }
}