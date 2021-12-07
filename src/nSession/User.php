<?php
namespace Nubersoft\nSession;

use \Nubersoft\Dto\Session\User\GetResponse;

class User
{
    /**
     *	@description	
     *	@param	
     */
    public static function get(): GetResponse
    {
        return new GetResponse(\Nubersoft\nApp::call()->getSession('user'));
    }
}