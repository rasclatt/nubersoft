<?php

namespace Nubersoft\JWTI;

use \Firebase\JWT\JWT;
use \Nubersoft\{
    nApp,
    JWTI
};

/**
 * @description 
 */
class Firebase extends nApp implements JWTI
{
    private $def_alg = 'HS256';
    private $settings;
    private $algo = ['HS256'];
    private $key;

    public  $response, $token;

    public function encode($body)
    {
        $settings = [
            "iss" => (!empty($this->settings['iss'])) ? $this->settings['iss'] : $this->siteUrl(),
            "aud" => (!empty($this->settings['aud'])) ? $this->settings['aud'] : $this->siteUrl(),
            "iat" => (!empty($this->settings['iat'])) ? $this->settings['iat'] : time()
        ];

        return $this->token  = JWT::encode(array_merge($settings, $body), $this->getKey(), $this->def_alg);
    }

    public function decode($token)
    {
        $this->token  = $token;
        return $this->response  = JWT::decode($this->token, $this->getKey(), $this->algo);
    }

    public function setKey($key)
    {
        $this->key  = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }
    /**
     * @description 
     */
    public function setAlgo($algo, $reset = false)
    {
        if ($reset)
            $this->algo = (is_array($algo)) ? $algo : [$algo];
        else
            $this->algo =  (is_array($algo)) ? array_merge($this->algo, $algo) : array_merge($this->algo, [$algo]);

        return $this;
    }
    /**
     * @description 
     */
    public function setAttr($name, $value)
    {
        $this->settings[$name]  = $value;
        return $this;
    }
}