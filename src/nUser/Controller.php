<?php
namespace Nubersoft\nUser;

use \Nubersoft\nSession\User;

class Controller extends \Nubersoft\nUser
{
    public function isLoggedIn(): bool
    {
        return (!empty(User::get()->username));
    }
    
    public function isAdmin(): bool
    {
        $user = User::get();
        if(empty($user->username))
            return false;
        
        if(!is_numeric($user->usergroup)) {
            $user->usergroup = constant($user->usergroup);
        }
        
        return (!defined('NBR_ADMIN'))? false : ($user->usergroup <= NBR_ADMIN);
    }
}