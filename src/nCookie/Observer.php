<?php
namespace Nubersoft\nCookie;

use \Nubersoft\ {
    nApp,
    nCookie,
    nObserver
};

/**
 * @description 
 */
class Observer extends nCookie implements nObserver
{
    private $nApp;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(nApp $nApp)
    {
        $this->nApp = $nApp;
    }
    /**
     * @description 
     */
    public function listen()
    {
    }
    /**
     * @description 
     */
    public function setCurrentPage()
    {
        if ($this->nApp->getServer())
            return false;

        $args = func_get_args();
        $duration = (defined('SESSION_EXPIRE_TIME')) ? SESSION_EXPIRE_TIME : 3600;
        $path = '/';
        $domain = null;

        if (!empty($args[0]) && is_array($args[0])) {
            $duration = (isset($args[0][0])) ? $args[0][0] : $duration;
            $path = (isset($args[0][1])) ? $args[0][1] : $path;
            $domain = (isset($args[0][2])) ? $args[0][2] : $domain;
        } else {
            $duration = $args[0];
        }

        # Set default incase referrer is not set
        $referrer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->nApp->siteUrl();

        $this->set('nbr_current_page', [
            'self' => $_SERVER['PHP_SELF'],
            'request' => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/',
            'referrer' => $referrer
        ], $duration, $path, $domain);

        return $this;
    }
}
