<?php
namespace Nubersoft;
/**
 *	@description	
 */
class nCookie extends nSession
{
	/**
	 *	@description	
	 */
	public	function set($key, $value = false, $duration = 3600, $path = '/', $domain = null)
	{
		$SESS			=	$this->getDataNode('_COOKIE');
		$SESS[$key]		=	$value;
		$this->removeNode('_COOKIE');
		$this->setNode('_COOKIE', $SESS);
		$value			=	((is_array($value))? json_encode($value) : $value);
		setcookie($key, $value, time()+$duration, $path, $domain);
	}
	/**
	 *	@description	
	 */
	public	function get($key, $real = true)
	{
		if(!$real) {
			$SESS			=	$this->getDataNode('_COOKIE');
			return (isset($SESS[$key]))? $this->decoder($SESS[$key]) : false;
		}
		return (isset($_COOKIE[$key]))? $this->decoder($_COOKIE[$key]) : false;
	}
	/**
	 *	@description	
	 */
	public	function decoder($value)
	{
		$val	=	@json_decode($value, 1);
		return (!empty($val))? $val : $value;
	}
	/**
	 *	@description	
	 */
	public	function destroy($key = false, $path = '/', $domain = null)
	{
		$SESS	=	$this->getDataNode('_COOKIE');
		$this->removeNode('_COOKIE');
		if(!empty($key)) {
			$this->set($key, false, strtotime('now - 1 hour'), $path, $domain);
			if(isset($SESS[$key]))
				unset($SESS[$key]);
			
			return true;
		}
		foreach($SESS as $k => $v) {
			$this->destroy($k);
		}
		$this->setNode('_COOKIE', []);
	}
}