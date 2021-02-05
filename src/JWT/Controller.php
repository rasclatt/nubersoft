<?php
namespace Nubersoft\JWT;
/**
 *	@description	
 */
class Controller extends \Nubersoft\JWT
{
	/**
	 *	@description	
	 */
	public function isExpired()
	{
        return $this->getExpiration('expired');
	}
	/**
	 *	@description	
	 */
	public function getData($token)
	{
        $this->data =   (!$this->isExpired())? $this->valid($token) : false;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function __call($method, $args = false)
	{
        $key    =   preg_replace('/^get/', '', $method);
        
        if(isset($this->data[$key]))
            return $this->data[$key];
        
        $key    =   trim(strtolower(implode('_', preg_split('/(?=[A-Z])/', $key))), '_');
        
        if(isset($this->data[$key]))
            return $this->data[$key];
        
        return ($key == 'all')? $this->data : false;
	}
	/**
	 *	@description	
	 */
	public static function getJwtPath()
	{
        return NBR_CLIENT_DIR.DS.'settings'.DS.'.jwttoken';
	}
	/**
	 *	@description	
	 */
	public static function getJwtTokenSecret()
	{
        $f  =   self::getJwtPath();
        if(!is_file($f))
            return false;
        
        return trim(file_get_contents($f));
	}
}