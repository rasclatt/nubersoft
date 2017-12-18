<?php
namespace Nubersoft;

class nAlert extends NubeData
{
	# Create a static array for storage
	protected	static $alerts	=	[];
	/**
	*	@description	Save an "warning" alert
	*/
	public	function saveAlert($msg, $type = 'general',$persist = false)
	{
		$this->doGlobalSave($msg,$type,'alert',$persist);
		return $this;
	}
	/**
	*	@description	Save an "success" alert
	*/
	public	function saveSuccess($msg, $type = 'general',$persist = false)
	{
		$this->doGlobalSave($msg,$type,'success',$persist);
		return $this;
	}
	/**
	*	@description	Save an "error" alert
	*/
	public	function saveError($msg, $type = 'general',$persist = false)
	{
		$this->doGlobalSave($msg,$type,'error',$persist);
		return $this;
	}
	/**
	*	@description	Clear session-based alerts
	*/
	public	function clearPresist($kind,$type,$all = false)
	{
		$nApp		=	nApp::call();
		$Session	=	$nApp->getHelper('nSessioner');
		if($all) {
			$SESSION	=	$this->toArray($nApp->getSession());
			if(empty($SESSION)) {
				trigger_error('Session appears to be empty. Make sure it has already been set',E_USER_WARNGING);
				return true;
			}
			
			foreach($SESSION as $key => $value) {
				if(strpos($key,'message_pool_') !== false) {
					$Session->destroy($key);
				}
			}
			
			return true;
		}
		
		$pool		=	'message_pool_'.$kind.'_'.$type;
		$Session	=	$nApp->getHelper('nSessioner');
		$Session->destroy($pool);
		return (empty($nApp->getSession($pool)));
	}
	/**
	*	@description	Saves any kind of alert
	*/
	public function doGlobalSave($msg,$type,$kind,$persist = false)
	{
		$type	=	(empty($type))? 'general' : $type;
		if($persist) {
			$pool		=	'message_pool_'.$kind.'_'.$type;
			$nApp		=	nApp::call();
			
			if(!empty($nApp->getSession($pool))) {
				
				$messages	=	$this->toArray($nApp->getSession($pool,true));
				
				if(is_array($messages))
					array_push($messages,$msg);
				else
					$messages	=	[$messages];

				$msg	=	$messages;
			}
			else
				$msg	=	(is_array($msg))? $msg : [$msg];
			
			$nApp->getHelper('nSessioner')->setSession($pool,array_unique($msg),true);
		}
		else
			# Store the data
			self::$alerts[$kind][$type][]	=	$msg;
		
		return $this;
	}
	
	public	static	function getStoredMessage($kind,$type)
	{
		$nApp		=	nApp::call();
		$pool		=	'message_pool_'.$kind.'_'.$type;
		$messages	=	$nApp->toArray($nApp->getSession($pool,true));
		
		if(empty($messages))
			return false;
		
		return $messages;
	}
	
	public	static	function getMessage($kind,$type)
	{
		return (isset(self::$alerts[$kind][$type]))? self::$alerts[$kind][$type] : false;
	}
	
	public	function getMessages()
	{
		return self::$alerts;
	}
	
	public	function sortMessageTypes($name)
	{
		$isType	=	[];

		if(stripos($name,'to') !== false)
			$isType['to']	=	true;

		if(stripos($name,'core') !== false)
			$isType['persist']	=	true;

		if(stripos($name,'admin') !== false)
			$isType['admin']	=	true;

		if(stripos($name,'alert') !== false)
			$isType['type']	=	'alert';
		elseif(stripos($name,'error') !== false)
			$isType['type']	=	'error';
		else
			$isType['type']	=	'success';

		return $isType;
	}
	
	public	function extractAllMessagesFromStringKeys($array)
	{
		$storage	=	[];
		foreach($array as $key => $value) {
			if(strpos('message_pool_',$key) === false)
				continue;

			$msgType	=	$nAlert->sortMessageType($key);

			if(isset($msgType['admin'])) {
				$storage['admin'][$msgType['type']][]	=	$value;
			}
			elseif(strpos('general',$key) !== false) {
				$storage['general'][$msgType['type']][]	=	$value;
			}
			else
				$storage['other'][$msgType['type']][]	=	$value;
		}
		
		return $storage;
	}
	/**
	*	@description	Returns all the messages in the session
	*/
	public	function getAllStoredMessages($clear=true)
	{
		$nApp		=	nApp::call();
		$storage	=	[];
		$SESSION	=	$this->toArray($nApp->getSession());

		if(is_array($SESSION)) {
			foreach($SESSION as $key => $value) {
				if(strpos($key,'message_pool_') === false)
					continue;
					
				$msgType	=	$this->sortMessageTypes($key);

				if(isset($msgType['admin'])) {
					$storage[$msgType['type']]['admin']	=	$value;
				}
				elseif(strpos($key,'general') !== false) {
					$storage[$msgType['type']]['general']	=	$value;
				}
				else {
					$newKey	=	str_replace(['message_pool','success','error','alert','admin','general','_'],'',$key);
					$storage[$msgType['type']][$newKey]	=	$value;
				}

				if($clear) {
					if(isset($_SESSION[$key]))
						unset($_SESSION[$key]);
					
					unset($SESSION[$key]);
				}
			}
			
			$nApp->saveSetting('_SESSION',$SESSION,true);
		}
		
		return $storage;
	}
	/**
	*	@description	Returns all the messages in the session and internal arrays
	*/
	public	function getSystemMessages($kind='',$clear=true)
	{
		$use			=	[];
		$stored			=	$this->getAllStoredMessages($clear);
		$internal		=	$this->getMessages();
		$hasStored		=	(is_array($stored) && !empty($stored));
		$hasInternal	=	(is_array($internal) && !empty($internal));
		
		switch(true){
			case($hasStored && $hasInternal):
				$final	=	[];
				foreach($stored as $type => $row) {
					
					foreach($row as $tk => $msgRow) {

						if(isset($internal[$type][$tk]))
							$final[$type][$tk]	=	array_merge($internal[$type][$tk],$stored[$type][$tk]);
						else
							$final[$type][$tk]	=	$msgRow;
					}
				}
				
				foreach($internal as $type => $row) {
					foreach($row as $tk => $msgRow) {
						if(isset($stored[$type][$tk]))
							$final[$type][$tk]	=	array_merge($stored[$type][$tk],$internal[$type][$tk]);
						else
							$final[$type][$tk]	=	$msgRow;
					}
				}
				
				$use	= $final;
				break;
			case($hasStored && !$hasInternal):
				$use 	=	$stored;
				break;
			case(!$hasStored && $hasInternal):
				$use	=	$internal;
		}
		
		if(empty($use))
			return [];
		
		if(!empty($kind))
			return (isset($use[$kind]))? $use[$kind] : [];
		
		return $use;
	}
	/**
	*	@description	Returns any sort of array
	*/
	public	function __call($name,$args=false)
	{
		# Strip out the get part of the name
		$key	=	strtolower(str_replace('get','',$name));
		# Store default
		$arg	=	(!empty($args[0]))? $args[0] : 'general';
		# If there is an alert already
		if(isset(self::$alerts[$key]))
			return (isset(self::$alerts[$key][$arg]))? self::$alerts[$key][$arg] : false;

		return false;
	}
	/**
	*	@description	Alias of __call()
	*/
	public	static function __callStatic($name,$args=false)
	{
		$args	=	(!empty($args[0]))? $args[0] : false;
		return nApp::call()->getHelper('nAlert')->{$name}($args);
	}
}