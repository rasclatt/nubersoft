<?php
namespace Nubersoft;

class nApp extends \Nubersoft\nFunctions
{
	use nUser\enMasse,
		Plugin\enMasse,
		nRouter\enMasse,
		DataNode\enMasse;
	
	private	static	$singleton,
					$Reflect;
	
	public	function __construct()
	{
		if(self::$singleton instanceof \Nubersoft\nApp)
			return self::$singleton;
		
		self::$singleton	=	$this;
		
		return self::$singleton;
	}
	
	public	function userGet($key = false)
	{
		$SESS	=	(!empty($this->getSession('user')))? $this->getSession('user') : [];
		
		if(!empty($key))
			return (isset($SESS[$key]))? $SESS[$key] : false;
		
		return $SESS;
	}
	
	public	function fetchUniqueId($other = false, $sub = 20)
	{
		return substr(date('YmdHis').rand(1000000, 9999999).$other, 0, $sub);
	}
	
	public	function getPost($key = false, $encode = true)
	{
		$data	=	$this->getGlobal('POST', $key);
		return ($encode)? $this->getHelper("nGlobal")->sanitize($data) : $data;
	}
	
	public	function getGet($key = false, $encode = true)
	{
		$data	=	$this->getGlobal('GET', $key);
		
		return ($encode)? $this->getHelper("nGlobal")->sanitize($data) : $data;
	}
	
	public	function getRequest($key = false, $encode = true)
	{
		$data	=	$this->getGlobal('REQUEST', $key);
		return ($encode)? $this->getHelper("nGlobal")->sanitize($data) : $data;
	}
	
	public	function getHelper()
	{
		$args		=	func_get_args();
		$class		=	(!empty($args[1]))? $args[0] : str_replace('\\\\', '\\', "\\Nubersoft\\".$args[0]);
		try {
			$Reflect	=	$this->getReflector();
			return $Reflect->execute($class);
		}
		catch(\Exception $e) {
			throw new HttpException('Class doesn\'t exist: <pre>'.print_r(array_map(function($v){ return (isset($v['file']))? str_replace(NBR_ROOT_DIR, '', $v['file']).'('.$v['line'].')' : $v; },debug_backtrace()),1).'</pre>', 100);
		}
	}
	
	public	function getHelperClass($class)
	{
		return $this->getHelper($class, 1);
	}
	
	public	static function call($class = false, $plugin= false)
	{
		return (!empty($class))? (new nApp())->getHelper($class, $plugin) : new nApp();
	}
	
	public	static	function createContainer($func, $cache=false)
	{
		$Reflect	=	(new nApp())->getReflector();
		
		if($cache) {
			ob_start();
			$Reflect->reflectFunction($func);
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
		else
			return $Reflect->reflectFunction($func);
	}
	
	public	function getReflector()
	{
		if(self::$Reflect instanceof \Nubersoft\nReflect)
			return self::$Reflect;
		
		self::$Reflect	=	new nReflect();
		
		return self::$Reflect;
	}
	
	public	function getDataNode($key = false)
	{
		return $this->getHelper('DataNode')->getDataNode($key);
	}
	
	public	function decode($value)
	{
		return json_decode($this->dec($value),true);
	}
	
	public	function encode($value)
	{
		return $this->enc(json_encode($value));
	}
	
	public	function enc($value)
	{
		if(is_array($value) || is_object($value))
			return $value;
		
		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}
	
	public	function dec($value)
	{
		if(is_array($value) || is_object($value))
			return $value;
		
		return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
	}
	
	public	function getServer($key = false, $encode = true)
	{
		$data	=	$this->getGlobal('SERVER', $key);
		return ($encode)? $this->getHelper("nGlobal")->sanitize($data) : $data;
	}
	
	public	function getSession($key = false)
	{
		$SESS	=	$this->getDataNode('_SESSION');
		
		if($key)
			return (!empty($SESS[$key]))? $SESS[$key] : false;
		
		if(is_array($SESS))
			ksort($SESS);
		
		return $SESS;
	}
	
	public	function getAdminPage($key = 'full_path')
	{
		return $this->getHelper('Settings\Admin')->{__FUNCTION__}($key);
	}
	
	public	function isAdminPage()
	{
		$this->getHelper('Settings\Admin')->{__FUNCTION__}();
	}
	
	public	function saveSetting($key, $value, $clear = false)
	{
		$DataNode	=	$this->getHelper('DataNode');
		
		if($clear)
			$DataNode->removeNode($key);
		
		$DataNode->addNode($key, $value);
	}
	
	public	function getFiles()
	{
		return $this->getDataNode('_FILES');
	}
	
	public	function reportErrors($rep = true)
	{	
		ini_set('display_errors', $rep);
	
		if($rep)
			error_reporting(E_ALL);
		
		return $this;
	}
}