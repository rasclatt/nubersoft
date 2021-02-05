<?php
namespace Nubersoft\nToken;

use \Nubersoft\ {
    nToken,
    nObserver,
    JWTFactory as JWT,
    Exception\Ajax as AjaxError
};
/**
 *	@description	
 */
class Observer extends nToken implements nObserver
{
    private $JWT;
	/**
	 *	@description	
	 */
	public function __construct()
	{
        $this->JWT  =   JWT::get();
	}
	/**
	 *	@description	Validate a previously set JWT token
	 */
	public function validateJWT()
	{
        try {
            $this->JWT->get($this->getSession('jwtToken'));
        }
        catch (\Exception $e) {
            # If invalid, check to see if a post is being run
            if(!empty($this->getPost())) {
                # Stop if there is a post request
                if($this->isAjaxRequest())
                    throw new AjaxError('Invalid request', 403);
                else
                    throw new \Exception('Invalid request', 403);
            }
        }
        
        return $this;
	}
	/**
	 *	@description	These are all the actions that don't require a token
	 */
    private static  $skipped_services   =   [];
	/**
	 *	@description	
	 */
	public function listen()
	{
        # Don't do anything if nothing is happening
        if(empty($this->getPost()))
            return false;
        # Check if the current action is being skipped for validation.
        # Ideally, these actions have their own tokens being validated
        if(in_array($this->getPost('action'), self::$skipped_services))
            return false;
        # See if this option is set
        if(!defined('STRICT_CSRF'))
            return false;
        # If set, see if it's active
        elseif(STRICT_CSRF == 'off' || empty(STRICT_CSRF))
            return false;
        # See if there is an ajax token
        if(!empty($this->getPost('deliver')['token'])) {
            # Not matched, then invalid
            if(!$this->match('page', $this->getPost('deliver')['token'])) {
                throw new \Nubersoft\HttpException($this->getHelper('ErrorMessaging')->getMessage('invalid_tokenmatch', $this->getSession('locale'), $this->getSession('locale_lang')), 403);
            }
            # If valid stop
            return false;
        }
        # If not ajax, check the page token and exit to error if no match
        if(!$this->match('page', $this->getPost('token')['nProcessor'])) {
            throw new \Nubersoft\HttpException($this->getHelper('ErrorMessaging')->getMessage('invalid_tokenmatch', $this->getSession('locale'), $this->getSession('locale_lang')), 403);
        }
	}
	/**
	 *	@description	Adds skippable actions
	 */
	public function addSkipService($val)
	{
        if(!is_array($val))
            $val    =   [$val];
        foreach($val as $v) {
            self::$skipped_services[]   =   $v;
        }
        return $this;
	}
}