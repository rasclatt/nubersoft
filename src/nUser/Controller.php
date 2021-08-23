<?php
namespace Nubersoft\nUser;

use \Nubersoft\Dto\Session\User\GetResponse;

class Controller extends \Nubersoft\nUser
{
    public function isLoggedIn(): bool
    {
        $user = new GetResponse(($this->getDataNode('_SESSION')['user'])?? []);
        return (!empty($user->username));
    }
    
    public function isAdmin(): bool
    {
        $SESS = $this->getDataNode('_SESSION');
        $user = new GetResponse((!empty($SESS['user']))? $SESS['user'] : null);
        if(empty($user->username))
            return false;
        
        if(!is_numeric($user->usergroup)) {
            $user->usergroup = constant($user->usergroup);
        }
        
        return (!defined('NBR_ADMIN'))? false : ($user->usergroup <= NBR_ADMIN);
    }
}