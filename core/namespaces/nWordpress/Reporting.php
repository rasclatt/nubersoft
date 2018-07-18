<?php
namespace nWordpress;

class Reporting extends \Nubersoft\nApp
{
	private	$err	=	[
		'checkout' => [
			'001' => 'Cart is unavailable.'
		],
		'cart' => [
			'001' => 'Cart is unavailable.'
		],
		'login' => [
			'001' => 'Incorrect Username and/or Password'
		],
		'register' => [
			'001' => 'Username is already taken.'
		],
		'email' => [
			'001' => 'Address can not be left empty'
		]
	];
	
	public	function getErrors()
	{
	}
	
	public	function getErrorMsg($family,$code)
	{
		if(!empty($this->err[$family][$code]))
			return $this->err[$family][$code];
		
		return 'Unknown Error.';
	}
	
	public	function getRemoteMsg($action,$family,$code)
	{
		$msg = THEME_CLIENT_SETTINGS.DS.'msg'.DS.$action.'.'.$family.'.'.$code.'.txt';
		
		if(is_file($msg))
			return file_get_contents($msg);
	}
	
	public	function setMsg($msg,$family,$code)
	{
		$this->err[$family][$code]	=	$msg;
		
		return $this;
	}
}