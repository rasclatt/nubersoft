<?php
namespace Nubersoft;

class	Cryptor extends \Nubersoft\Encryption
	{
		public		$salt,
					$ivsize;

		protected	$urlenc;

		public	function __construct($salt = false,$urlenc = false)
			{
				if(!empty($salt))
					$this->salt	=	$salt;
					
				$this->urlenc	=	$urlenc;
			}

		public	function saltkey($salt = false)
			{
				$this->salt		=	(!isset($this->salt) && $salt != false)? $salt:$this->salt;
				$hex			=	crypt($this->salt, '$2a$07$'.$this->salt.'$');
				$crc32			=	crc32($hex);
				$packed			=	pack('H*', sprintf('%u',$crc32));
				$b64			=	substr(base64_encode(md5($packed)),0,16);
				$key			=	rtrim($b64);
				return $key;
			}

		public	function encrypt($text = '', $salt = false)
			{
				if(!function_exists("mcrypt_get_iv_size"))
					return false;
				
				$this->salt		=	(!isset($this->salt) && $salt != false)? $salt:$this->salt;
				$key			=	$this->saltkey($this->salt);
				$key_size		=	strlen($key);
				$iv_size		=	mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$this->ivsize[]	=	$iv_size;
				$iv				=	mcrypt_create_iv($iv_size, MCRYPT_RAND);
				$ciphertext		=	mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
				$ciphertext		=	$iv.$ciphertext;
				return ($this->urlenc == true)? urlencode(trim(base64_encode($ciphertext))): trim(base64_encode($ciphertext));
				
			}

		public	function decrypt($text ='', $salt = false)
			{
				try {
					$this->salt			=	(!isset($this->salt) && $salt != false)? $salt : $this->salt;
					$ciphertext_dec		=	trim(base64_decode($text));
					$key				=	$this->saltkey($this->salt);
					$iv_size			=	mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
					$this->ivsize[]		=	$iv_size;
					$iv_dec				=	substr($ciphertext_dec, 0, $iv_size);
					$ciphertext_dec		=	substr($ciphertext_dec, $iv_size);
					if($this->urlenc == true)
						$decrypted	=	urldecode(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec));
					else
						$decrypted	=	mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
				}
				catch (Exception $e) {
					if($this->isAdmin()) {
						printpre($e);
						return;
					}
				}
				
				return $decrypted;
			}
	}