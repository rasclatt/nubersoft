<?php
namespace Nubersoft\API;

class Core extends \Nubersoft\cURL
{
	public	function __construct($query = false)
	{
		$this->sendHeader	=	false;
		$this->query		=	$query;
	}
	
	public	static	function fetch()
	{
		return (new Core())->remote(...func_get_args());
	}
	
	public	function remote()
	{
		$this->start();
		$args	=	func_get_args();
		$query	=	(!empty($args[0]))? $args[0] : false;
		$deJSON	=	(!empty($args[1]));
		$isPost	=	(!empty($args[2]));
		$_url	=	$this->endpoint;
		
		if(empty($_url))
			throw new nException('Endpoint can not be empty.');
		
		if(!empty($query)) {
			if($isPost) {
				foreach($query as $key=>$value)
					$this->sendPost([$key=>$value]);
			}
			else {
				if(is_array($query)) {
					$query	=	http_build_query($query);
				}
			}
			$_url	.=	'?'.$query;
		}
		
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
		$this->close();
		return $this;
	}
}