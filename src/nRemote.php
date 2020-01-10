<?php
namespace Nubersoft;
/**
 *	@description	
 */
class nRemote extends nApp
{
	use nDynamics;
	
	private		$url_base,
				$response,
				$errors,
				$url,
				$format,
				$attr,
				$def;
	
	protected	$con;
	/**
	*	@description	Establish our live and test urls from base url
	*/
	public	function __construct($endpoint, $attributes, $func = false)
	{
		# Reset these storage arrays
		$this->attr	=
		$this->def	=	null;
		# Set any attributes right off the bat
		$this->attr				=	$attributes;
		# Set the endpoint
		$this->url				=	(is_callable($func))? $func($endpoint) : $endpoint;
		# Store the endpoint for later reference
		$this->attr['endpoint']	=	$this->url;
		# Use parent construct
		return parent::__construct();
	}
	/**
	*	@description	Fetch the stored api credentials
	*	@param	$type	[string|bool] Fetch a specific key
	*/
	public	function getCreds($type = false)
	{
		# Send back requested
		if(!empty($type))
			return (isset($this->attr[$type]))? $this->attr[$type] : false;
		
		# Send all
		return $this->attr;
	}
	/**
	*	@description	Create a url to endpoint for the service request
	*	@param	$path	[string] needs to include a path and/or query string
	*	@param	$environment	[string(live/test)] Changes the environment to which call is made
	*/
	public	function doService($path)
	{
		$this->url	.=	$path;
		return $this;
	}
	/**
	*	@description	Returns full endpoint url
	*/
	public	function getUrl()
	{
		return $this->url;
	}
	/**
	*	@description	Returns full endpoint url
	*/
	public	function setUrl($url)
	{
		$this->url	=	$url;
		return $this;
	}
	/**
	*	@description	Returns full endpoint url
	*/
	public	function getEndpoint()
	{
		return $this->attr['endpoint'];
	}
	/**
	*	@description	Adds header attribute
	*/
	public	function addHeader($key, $value)
	{
		$this->attr['header'][$key]	=	$key.': '.$value;
		return $this;
	}
	/**
	*	@description	
	*/
	public	function addSsl()
	{
		$this->addOption(CURLOPT_SSL_VERIFYPEER, 1)
			->addOption(CURLOPT_SSL_VERIFYHOST, 2);
		return $this;
	}
	/**
	*	@description	
	*/
	public	function addOption($key, $value)
	{
		$this->def[$key]	=	$value;
		return $this;
	}
	/**
	*	@description	
	*/
	public	function addAuth($username, $password)
	{
		$auth	=	$username.':'.$password;
		$this->addOption(CURLOPT_USERPWD, $auth)
			->addOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		
		$this->addHeader('Authorization', base64_encode($auth));
		
		return $this;
	}
	/**
	*	@description	Assemble all the settings to send to the endpoint 
	*	@param	$attr	[array|bool] Query string attributes via POST
	*	@param	$type	[string(get/post/delete/put)] Set the type of request
	*	@param	$return	[string] Set the type of data to return
	*/
	public	function query($attr = false, $settings = false, $type = 'get', $send = 'json', $return = 'json')
	{
		$this->format	=	$return;
		$this->def	=	[
			CURLOPT_URL	=> $this->url,
			CURLOPT_TIMEOUT => 15,
			# Send back from endpoint
			CURLOPT_RETURNTRANSFER => 1,
		];
		
		if(!empty($this->attr['header']))
			$this->def[CURLOPT_HTTPHEADER]	=	$this->attr['header'];
		
		# If post, add post request attributes
		if(in_array($type, ['post','put'])) {
			if(!empty($attr) && !is_array($attr)) {
				$this->def[CURLOPT_POST]			=	0;
				$this->def[CURLOPT_POSTFIELDS]	=	$attr;
			}
			else {
				if(empty($attr))
					$attr	=	[];

				$count	=	count($attr);

				$this->def[CURLOPT_POST]			=	$count;
				if($count > 0)
					$this->def[CURLOPT_POSTFIELDS]	=	($send == 'json')? json_encode($attr) : http_build_query($attr);
				else
					$this->def[CURLOPT_POSTFIELDS]	=	'';
			}
			if($type == 'put') {
				$this->def[CURLOPT_CUSTOMREQUEST]	=	"PUT";
			}
		}
		elseif(in_array($type, ['delete'])) {			
			if($type == 'delete') {
				$this->def[CURLOPT_CUSTOMREQUEST]	=	"DELETE";
			}
		}
		if(is_array($settings) && !empty($settings))
			$this->def	=	array_merge($this->def, $settings);
		# Start service
		$this->con	=	curl_init();
		//curl_setopt($this->con, CURLOPT_URL, $this->url);
		# Set the options for service
		curl_setopt_array($this->con, $this->def);
		# Send the request
		$this->response	=	curl_exec($this->con);
		# Store any errors
		$this->errors	=	[
			'error'	=>	curl_error($this->con),
			'error_code' => curl_getinfo($this->con, CURLINFO_HTTP_CODE),
			'response' => $this->response
		];
		# Close the request
		curl_close($this->con);
		# Return self to chain
		return $this;
	}
	/**
	*	@description	Return the results of the string 
	*	@param	$decode	[bool] Decode the response before sending back response
	*/
	public	function getResults($decode = true)
	{
		# Response includes the headers for whatever reason, so remove that and just isolate the data
		if(stripos($this->response,'Content-Length:') !== false) {
			$arr			=	explode(PHP_EOL,$this->response);
			$this->response	=	trim(array_pop($arr));
		}
		
		if($decode)
			return ($this->format == 'json')? json_decode($this->response, true) : simplexml_load_string($this->response);
		else
			return $this->response;
	}
	
	public	function getErrors()
	{
		return $this->errors;
	}
}