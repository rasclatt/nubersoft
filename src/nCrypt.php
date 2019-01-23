<?php
namespace Nubersoft;
/**
 *	@description	
 */
class nCrypt extends \Nubersoft\nApp
{
	private	$enctype	=	'AES-256-CBC';
	
	public	function encOpenSSLUrl($value)
	{
		return urlencode(base64_encode($this->encOpenSSL($value)));
	}
	
	public	function decOpenSSLUrl($value)
	{
		return $this->decOpenSSL(base64_decode(urldecode($value)));
	}
	/**
	 *	@description
	 */
	public	function encOpenSSL($value, $enctype = false)
	{
		if(!empty($enctype))
			$this->enctype	=	$enctype;
		
		$salt	=	
		$iv		=	false;
		$this->getKeys($iv, $salt);
		return openssl_encrypt(base64_encode($value), $this->enctype, $salt, 0, $iv); //OPENSSL_RAW_DATA
	}

	public	function decOpenSSL($value, $enctype = false)
	{
		if(!empty($enctype))
			$this->enctype	=	$enctype;
		
		$salt	=	
		$iv		=	false;
		$this->getKeys($iv, $salt);
		return base64_decode(openssl_decrypt($value, $this->enctype, $salt, 0, $iv)); //OPENSSL_RAW_DATA
	}
		
	private	function getKeys(&$iv, &$salt)
	{
		$salt	=	NBR_CLIENT_CACHE.DS.'encryption'.DS.'open_ssl_salt.pref';
		$iv		=	NBR_CLIENT_CACHE.DS.'encryption'.DS.'open_ssl_iv.pref';

		if(!is_file($salt)) {
			$this->isDir(pathinfo($salt, PATHINFO_DIRNAME), 1);
			$this->isDir(pathinfo($iv, PATHINFO_DIRNAME), 1);
			
			file_put_contents($salt, OPENSSL_SALT);
			file_put_contents($iv, OPENSSL_IV);
		}

		$iv		=	file_get_contents($iv);
		$salt	=	file_get_contents($salt);
	}
}