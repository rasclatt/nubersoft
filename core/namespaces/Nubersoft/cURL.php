<?php
namespace Nubersoft;

class 	cURL
{
	protected	$response,
				$ch,
				$sendHeader,
				$postFields,
				$endpoint,
				$errors,
				$query;
	protected	$userAgent	=	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56';

	public	function __construct($query = false)
	{
		$this->sendHeader	=	false;
		$this->query		=	$query;
		// Remote Connect
		$this->initConnect();
		if(!empty($this->query)) {
			if(!is_array($this->query))
				$this->response	=	$this->connect($this->query);
			else
				$this->encode();
		}
	}

	public	function initConnect()
	{
		$this->ch 	= curl_init();
		return $this;
	}

	public	function start()
	{
		$this->ch 	= curl_init();

		return $this;
	}

	public	function close()
	{
		curl_close($this->ch);
	}

	public	function sendPost($array = array())
	{
		$this->postFields['payload']	=	$array;
		$this->postFields['query']		=	http_build_query($array);
		return $this;
	}

	public	function setAttr($attr = false,$val = false)
	{
		if(!empty($attr)) {
			curl_setopt($this->ch, $attr, $val);
		}

		return $this;
	}

	public	function connect()
	{
		$args	=	func_get_args();
		$_url	=	(!empty($args[0]) && is_string($args[0]))? $args[0] : false;
		$deJSON	=	(!empty($args[1]));
		$close	=	(!empty($args[2]));
		$return	=	(isset($args[3]) && is_bool($args[3]))? $args[3] : true;
		
		if(empty($_url))
			$_url	=	$this->endpoint;
			
		if(empty($_url))
			throw new nException('Endpoint can not be empty.');
		
		$this->setAttr(CURLOPT_URL, $_url)
			->setAttr(CURLOPT_RETURNTRANSFER, 1);

		if(strpos($_url,"https://") !== false) {
			$this->setAttr(CURLOPT_SSL_VERIFYPEER,2)
				->setAttr(CURLOPT_SSL_VERIFYHOST,2);
		}

		if(!empty($this->postFields['payload'])) {
			$this->setAttr(CURLOPT_POST, count($this->postFields['payload']))
				->setAttr(CURLOPT_POSTFIELDS, $this->postFields['query']);
		}

		if(!empty($this->sendHeader))
			$this->setAttr(CURLOPT_USERAGENT, $this->userAgent);

		$decode			=	curl_exec($this->ch);
		$this->response	=	($deJSON)? json_decode($decode, true) : $decode;
		$error			=	curl_error($this->ch);

		if($close)
			curl_close($this->ch);

		if(!$return)
			return $this;

		return (empty($error))? $this->response: $error;
	}

	public	function emulateBrowser()
	{
		$this->sendHeader	=	true;
		return $this;
	}

	public	function setTimeOut($val1 = 10,$val2 = 15)
	{
		if($val2 < $val1)
			$val2	=	$val1+5;

		$this->setAttr(CURLOPT_CONNECTTIMEOUT, $val1)
		->setAttr(CURLOPT_TIMEOUT, $val2);

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

	public	function getResource()
	{
		return $this->ch;
	}

	public	function getResponse($decode = false)
	{
		return ($decode)? json_decode($this->response,true) : $this->response;
	}

	public	function getContents($url,$decode = true)
	{
		$content	=	file_get_contents($url);
		if(empty($content))
			return;

		return ($decode)? json_decode($content,true) : $content;
	}

	public	function query($url)
	{
		$this->setEndpoint($url)
			->start()
			->send()
			->close();

		return $this;
	}

	public	function setEndpoint($url)
	{
		$this->endpoint	=	$url;
		return $this;
	}

	public	function send($return = true)
	{
		$this->setAttr(CURLOPT_URL, $this->endpoint);

		if($return)
			$this->setAttr(CURLOPT_RETURNTRANSFER, 1);

		if(strpos($this->endpoint,"https://") !== false) {
			$this->setAttr(CURLOPT_SSL_VERIFYPEER,2)
				->setAttr(CURLOPT_SSL_VERIFYHOST,2);
		}

		if(!empty($this->postFields['payload'])) {
			$this->setAttr(CURLOPT_POST, count($this->postFields['payload']))
				->setAttr(CURLOPT_POSTFIELDS, $this->postFields['query']);
		}

		if(!empty($this->sendHeader))
			$this->setAttr(CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56');

		$this->response	=	curl_exec($this->ch);
		$this->errors[]	=	curl_error($this->ch);

		return $this;
	}
}