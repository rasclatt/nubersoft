<?php
namespace Nubersoft\nUser;

class Controller extends \Nubersoft\nUser
{
    private    static    $Session;
    
    public    function isLoggedIn()
    {
        return (!empty($this->getDataNode('_SESSION')['user']['username']));
    }
    
    public    function isAdmin()
    {
        $SESS    =    $this->getDataNode('_SESSION');
        $user    =    (!empty($SESS['user']))? $SESS['user'] : false;
        
        //echo printpre($user);
        
        if(empty($user['username']))
            return false;
        
        if(!is_numeric($user['usergroup'])) {
            $user['usergroup']    =    constant($user['usergroup']);
        }
        
        if(!defined('NBR_ADMIN'))
            return false;
        else
            return ($user['usergroup'] <= NBR_ADMIN);
    }
}