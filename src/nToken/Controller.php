<?php
namespace Nubersoft\nToken;

class Controller extends \Nubersoft\nToken
{
    public function validCSRF($token, $key = 'login')
    {
        # Make sure the token is not currently empty
        if (empty($token)) {
            $matched = false;
        } else {
            # Fetch token engine
            $Token = $this->getHelper('nToken');
            # Match the login token vs post token
            $matched = $Token->match($key, $token);
        }
        # Stop if they don't match
        if (!$matched) {
            $this->toError($this->getHelper('ErrorMessaging')->getMessageAuto('invalid_token'), false, false);
            return false;
        }
        # Matched
        return true;
    }
}
