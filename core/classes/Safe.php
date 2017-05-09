<?php

	class	Safe
		{
			public	static	function encode($_payload)
				{
					$_payload	=	htmlentities($_payload,ENT_QUOTES);
					$encoded	=	htmlspecialchars(htmlspecialchars($_payload, ENT_QUOTES), ENT_QUOTES);
					return	$encoded;
				}
			
			public	static	function encodeSingle($_payload,$quotes = true)
				{
					if($quotes)
						$decoded	=	htmlentities($_payload,ENT_QUOTES);
					else
						$decoded	=	htmlentities($_payload);
						
					return $decoded;
				}
				
			public	static	function decode($_payload)
				{
					$_payload	=	html_entity_decode($_payload,ENT_QUOTES);
					$decoded	=	htmlspecialchars_decode(htmlspecialchars_decode($_payload, ENT_QUOTES), ENT_QUOTES);

					return	$decoded;
				}
			
			public	static	function decodeSingle($_payload)
				{
					return self::decodeForm($_payload);
				}
			
			public	static	function decodeForm($_payload)
				{
					$_payload	=	html_entity_decode($_payload);
					$decoded	=	htmlspecialchars_decode($_payload);
					return $decoded;
				}
			
			protected	static	function preg()
				{
					$space				=	"\r\n";
					return $preggers	= "/[^a-zA-Z0-9\= \&\;\,\?\#\/\!\-_\.\{\}@:~\(\)\[\]\^\*\%\\\\$\|\s+".$space."]/";
				}
			
			
			public	static	function jSURL64($value = false)
				{
					return self::URL64(json_encode($value,JSON_FORCE_OBJECT));
				}
			
			public	static	function jSURL64_decode($value = false)
				{
					return (!empty($value))? json_decode(self::URL64_decode($value),true) : false;
				}
			
			public	static	function URL64_decode($value = false)
				{
					return (!empty($value))? base64_decode(urldecode($value)) : false;
				}
			public	static	function URL64($value = false)
				{
					return urlencode(base64_encode($value));
				}
				
			public	static	function PrettyURL($value)
				{
					return	str_replace(" ","-",preg_replace('/[^a-zA-Z0-9\-\_\s]/','',$value));
				}
			
			public	static	function bcrypt_encode($value = false,$salt = false)
				{
					if(empty($value))
						return false;
					
					$salt		=	(empty($salt))? NubeData::$settings->engine->file_salt : $salt;
					return urlencode(base64_encode(Encryption::Encrypt($value,$salt)));
				}
				
			public	static	function bcrypt_decode($value = false, $salt = false)
				{
					if(empty($value))
						return false;
					
					$filename	=	Safe::decode($value);
					$salt		=	(empty($salt))? NubeData::$settings->engine->file_salt : $salt;
		
					return Encryption::Decrypt(base64_decode(urldecode($value)),$salt);
				}
			
			public	static	function to_object($val = false)
				{
					if(empty($val))
						return $val;
						
					if(is_object($val) || is_array($val))
						return json_decode(json_encode($val,JSON_FORCE_OBJECT));
					else
						return $val;
				}
			
			public	static	function to_array($val = false)
				{
					if(empty($val))
						return $val;
						
					if(is_object($val) || is_array($val))
						return json_decode(json_encode($val,JSON_FORCE_OBJECT),true);
					else
						return $val;
				}
			
			public	static	function normalize_url($url = false)
				{
					return str_replace(DS.DS,DS,$url);
				}
			
			public	static	function encOpenSSL($value = false,$options = false)
				{
					$iv		=	(!empty($options['iv']) && is_numeric($options['iv']) && (strlen($options['iv']) == 16))? $options['iv'] :NubeData::$settings->engine->openssl_iv;
					$salt	=	(!empty($options['salt']) && (strlen($options['salt']) == 16))? $options['salt'] : NubeData::$settings->engine->openssl_salt;
					return urlencode(base64_encode(openssl_encrypt($value,'AES-256-CBC',$salt,OPENSSL_RAW_DATA,$iv)));
				}
			
			public	static	function decOpenSSL($value = false,$options = false)
				{
					$value	=	(!empty($options['urlencode']))? urlencode($value) : $value;
					$value	=	(!empty($options['base64']))? base64_decode($value) : $value;
					
					$iv		=	(!empty($options['iv']) && is_numeric($options['iv']) && (strlen($options['iv']) == 16))? $options['iv'] : NubeData::$settings->engine->openssl_iv;
					$salt	=	(!empty($options['salt']) && (strlen($options['salt']) == 16))? $options['salt'] : NubeData::$settings->engine->openssl_salt;
					return openssl_decrypt(base64_decode(urldecode($value)),'AES-256-CBC',$salt,OPENSSL_RAW_DATA,$iv);
				}
		}