<?php

	class 	cURL
		{
			public		$response;
			public		$ch;
			protected	$sendHeader;
			
			protected	$PostFields;
			
			private		$query;
			
			public	function	__construct($query = '')
				{
					$this->sendHeader	=	false;
					$this->query		=	$query;
					// Remote Connect
					$this->initConnect();
					if(!empty($this->query)) {
						if(!is_array($this->query))
							$this->response	=	$this->Connect($this->query);
						else
							$this->encode();
					}
				}
			
			public	function initConnect()
				{
					$this->ch 	= curl_init();
					return $this;
				}
			
			public	function SendPost($array = array())
				{
					$this->PostFields['payload']		=	$array;
					$this->PostFields['query']		=	http_build_query($array);
					return $this;
				}
			
			public	function setAttr($attr = false,$val = false)
				{
					if(!empty($attr))
						curl_setopt($this->ch, $attr, $val);
					
					return $this;
				}
			
			public	function Connect($_url,$deJSON = true)
				{
					curl_setopt($this->ch, CURLOPT_URL, $_url);
					curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
					
					if(strpos($_url,"https://") !== false) {
						curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER,2);
						curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST,2);
					}
					
					if(!empty($this->PostFields['payload'])) {
						curl_setopt($this->ch, CURLOPT_POST, count($this->PostFields['payload']));
						curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->PostFields['query']);
					}
					
					if(!empty($this->sendHeader))
						curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56');

					$decode		=	curl_exec($this->ch);
					$_response	=	($deJSON)? json_decode($decode, true) : $decode;
					$error		=	curl_error($this->ch);
					
					curl_close($this->ch);
					return (empty($error))? $_response: $error;
				}
			
			public	function emulateBrowser()
				{
					$this->sendHeader	=	true;
					return $this;
				}
			
			public	function encode($_filter = 0)
				{
					foreach($this->query as $key => $value) {
						$string[]	=	urlencode($key).'='.urlencode($value);
					}
						
					if($_filter == true)
						$string	=	array_filter($string);
						
					return implode("&",$string);
				}
		}