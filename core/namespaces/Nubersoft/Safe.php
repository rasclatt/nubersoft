<?php
namespace Nubersoft;

class Safe extends \Nubersoft\nFunctions
	{
		public	function encodeFunc($_payload)
			{
				$_payload	=	htmlentities($_payload,ENT_QUOTES);
				$encoded	=	htmlspecialchars(htmlspecialchars($_payload, ENT_QUOTES), ENT_QUOTES);
				return	$encoded;
			}
		
		public	function encodeSingleFunc($_payload,$quotes = true)
			{
				if($quotes)
					$decoded	=	htmlentities($_payload,ENT_QUOTES);
				else
					$decoded	=	htmlentities($_payload);
					
				return $decoded;
			}
			
		public	function decodeFunc($_payload)
			{
				if(!is_string($_payload))
					return $_payload;
				
				$_payload	=	html_entity_decode($_payload,ENT_QUOTES);
				$decoded	=	htmlspecialchars_decode(htmlspecialchars_decode($_payload, ENT_QUOTES), ENT_QUOTES);

				return	$decoded;
			}
		
		public	function decodeSingleFunc($_payload)
			{
				return $this->decodeFormFunc($_payload);
			}
		
		public	function decodeFormFunc($_payload)
			{
				if(is_object($_payload) || is_array($_payload)) {
					throw new \Exception('Input can not be an object or array'.printpre($_payload));
				}
				
				$_payload	=	(is_callable($_payload))? html_entity_decode($_payload()) : html_entity_decode($_payload);
				$decoded	=	htmlspecialchars_decode($_payload);
				return $decoded;
			}
		
		protected function pregFunc()
			{
				$space				=	"\r\n";
				return $preggers	= "/[^a-zA-Z0-9\= \&\;\,\?\#\/\!\-_\.\{\}@:~\(\)\[\]\^\*\%\\\\$\|\s+".$space."]/";
			}
		
		
		public	function jSURL64Func($value = false)
			{
				return $this->URL64Func(json_encode($value,JSON_FORCE_OBJECT));
			}
		
		public	function jSURL64_decodeFunc($value = false)
			{
				return (!empty($value))? json_decode($this->URL64_decodeFunc($value),true) : false;
			}
		
		public	function URL64_decodeFunc($value = false)
			{
				return (!empty($value))? base64_decode(urldecode($value)) : false;
			}
		public	function URL64Func($value = false)
			{
				return urlencode(base64_encode($value));
			}
			
		public	function PrettyURLFunc($value)
			{
				return	str_replace(" ","-",preg_replace('/[^a-zA-Z0-9\-\_\s]/','',$value));
			}
		
		public	function bcrypt_encodeFunc($value = false,$salt = false)
			{
				if(empty($value))
					return false;
				
				if(empty($salt))
					$this->getFileSalt($salt);
					
				return urlencode(base64_encode(Encryption::encrypt($value,$salt)));
			}
			
		public	function bcrypt_decodeFunc($value = false, $salt = false)
			{
				if(empty($value))
					return false;
				
				$filename	=	$this->decodeFunc($value);
				
				if(empty($salt))
					$this->getFileSalt($salt);
	
				return Encryption::decrypt(base64_decode(urldecode($value)),$salt);
			}
		
		public	function to_objectFunc($val = false)
			{
				return $this->toObject($val);
			}
		
		public	function to_arrayFunc($val = false)
			{
				return $this->toArray($val);
			}
		
		public	function normalize_urlFunc($url = false)
			{
				return str_replace(DS.DS,DS,$url);
			}
		
		public	function encOpenSSLFunc($value = false)
			{
				$salt	=	
				$iv		=	false;
				$this->getKeys($iv,$salt);
				return urlencode(base64_encode(openssl_encrypt($value,'AES-256-CBC',$salt,OPENSSL_RAW_DATA,$iv)));
			}
		
		public	function decOpenSSLFunc($value = false,$options = false)
			{
				$value	=	(!empty($options['urlencode']))? urlencode($value) : $value;
				$value	=	(!empty($options['base64']))? base64_decode($value) : $value;
				$salt	=	
				$iv		=	false;
				$this->getKeys($iv,$salt);
				return openssl_decrypt(base64_decode(urldecode($value)),'AES-256-CBC',$salt,OPENSSL_RAW_DATA,$iv);
			}
		
		private	function getKeys(&$iv,&$salt)
			{
				$nApp	=	nApp::call();
				$salt	=	$nApp->getCacheFolder().DS.'encryption'.DS.'open_ssl_salt.pref';
				$iv		=	$nApp->getCacheFolder().DS.'encryption'.DS.'open_ssl_iv.pref';
				
				if(!is_file($salt)) {
					$defines	=	$nApp->getHelper('GetSitePrefs')->defines();
					$nApp->saveFile($defines['OPENSSL_SALT'],$salt);
					$nApp->saveFile($defines['OPENSSL_IV'],$iv);
				}
				
				$iv		=	file_get_contents($iv);
				$salt	=	file_get_contents($salt);
			}
		
		public	function getFileSalt(&$salt)
			{
				$nApp	=	nApp::call();
				$salt	=	$nApp->getCacheFolder().DS.'encryption'.DS.'file_salt.pref';
				
				if(!is_file($salt)) {
					$defines	=	$nApp->getHelper('GetSitePrefs')->defines();
					$nApp->saveFile($defines['FILE_SALT'],$salt);
				}
				
				$salt	=	file_get_contents($salt);
			}
		
		public	static	function __callStatic($name,$args=false)
			{
				$name	=	"{$name}Func";
				$count	=	(is_array($args))? count($args) : 0; 
				
				if($count == 0)
					return (new Safe())->{$name}();
				elseif($count == 1)
					return (new Safe())->{$name}($args[0]);
				elseif($count == 2)
					return (new Safe())->{$name}($args[0],$args[1]);
			}
			
		public	function __call($name,$args=false)
			{
				$name	=	"{$name}Func";
				$count	=	(is_array($args))? count($args) : 0; 
				
				if($count == 0)
					return $this->{$name}();
				elseif($count == 1)
					return $this->{$name}($args[0]);
				elseif($count == 2)
					return $this->{$name}($args[0],$args[1]);
			}
		
		public	function sanitize($value,$preg = '/[^a-zA-Z\_\-0-9]/')
			{
				return preg_replace($preg,'',$value);
			}
	}