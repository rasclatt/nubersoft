<?php

namespace Nubersoft;

/**
 *	@description	
 */
class JWT extends nApp
{
    private $JWT, $key, $exp;
    private $params = [];
    private $unset = ['iss', 'aud', 'iat'];
    # Expiration
    const EXP = 18000;

    public $isExpired;

    public function __construct(JWTI $JWT, $key = false, $algo = false)
    {
        $this->JWT = $JWT;
        # Fetch the secret from the client folder
        $secret = JWT\Controller::getJwtTokenSecret();
        # If there is no secret, create temporary one
        if (!$secret)
            $secret = md5(rand(10000000, 1000000000));
        # Store key
        $this->key = (!empty($key)) ? $key : $secret;
        //$this->JWT->setAttr('iat', time() - 19000);
        # Set the key in object
        $this->JWT->setKey($this->key);
        # Auto-add the algorythm
        if (!empty($algo))
            $this->JWT->setAlgo($algo, true);
    }
    /**
     *	@description	
     */
    public function setParam(string $param, string $site)
    {
        $this->params[$param] = $site;
        return $this;
    }
    /**
     *	@description	
     */
    public function setAttr($k, $v)
    {
        if (method_exists($this->JWT, __FUNCTION__))
            $this->JWT->{__FUNCTION__}($k, $v);
        else
            throw new \Nubersoft\HttpException('Parent class has no "' . __FUNCTION__ . '()" method.');

        return $this;
    }

    public function create($body)
    {
        return $this->JWT->encode($body);
    }
    /**
     *	@description	
     */
    public function get($token)
    {
        $value = $this->JWT->decode($token);

        if (empty($value))
            return false;

        if (!is_array($value))
            return $this->toArray($value);

        return $value;
    }
    /**
     *	@description	
     */
    public function valid($token)
    {
        $issSite = (!empty($this->params['iss'])) ? $this->params['iss'] : $surl = preg_replace('!^https?://!', '', $this->siteUrl());
        $audSite = (!empty($this->params['aud'])) ? $this->params['aud'] : $surl;

        try {
            $content = $this->get($token);
            # Fetch the expiration
            $this->exp = (!empty($content['iat'])) ? ((int) $content['iat'] + self::EXP) : time();
            # Count all the validation points
            $validcount = array_sum([
                (!empty($content['iss']) && $content['iss'] == $issSite),
                (!empty($content['aud']) && $content['aud'] == $audSite),
                $this->isExpired = $this->exp < time()
            ]);
            # Check
            if ($validcount != 3) {
                return false;
            }

            foreach ($this->unset as $u) {
                if (isset($content[$u]))
                    unset($content[$u]);
            }

            return $content;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     *	@description	
     */
    public function getExpiration($name = false)
    {
        $exp = (is_numeric($this->exp)) ? [
            'datetime' => date('Y-m-d H:i:s', $this->exp),
            'timezone' => date_default_timezone_get(),
            'unix' => $this->exp,
            'expired' => time() > $this->exp
        ] : false;

        if (empty($exp))
            return false;

        if ($name)
            return ($exp[$name]) ?? true;

        return $exp;
    }
}
