<?php
namespace Nubersoft;

class	nSessioner extends \Nubersoft\nApp
{
	private	$opts,
			$sp_chars;

	const	SAFE	=	's';
	/*
	**	@param	[const] If value equals 's', the session values will save with htmlspecialchars etc.
	*/
	public	function __construct()
	{
		$this->sp_chars	=
		$this->opts		=	false;

		if(func_num_args() > 0) {
			$this->opts	=	func_get_args();

			if(in_array('s',$this->opts))
				$this->sp_chars	=	true;
		}

		return parent::__construct();
	}
	/*
	**	@description	Toggles sessions on or off
	*/
	public	function observer()
	{
		$use_session	=	(!defined('SESSION_ON'));
		if(defined('SESSION_ON'))
			$use_session	=	SESSION_ON;

		if($use_session) {
			if(!isset($_SESSION)) {
				$this->start();
			}
		}

		return $this;
	}

	public	function clearAlerts()
	{
		if(isset($_SESSION)) {
			if(!empty($_SESSION['alerts'])) {
				$_SESSION['alerts']	=	NULL;
				unset($_SESSION['alerts']);
			}
		}
	}
	/*
	**	@param	$array	[array]			Requires the associate data array
	**	@param	$filter	[bool | array]	If array, check that data array key(s) are in filter array. Set if not
	**	@param	$sName	[bool | string]	If string, will save a sub-array using this value
	*/
	public	function makeSession($array,$filter = false,$sName = false)
	{
		foreach($array as $key => $value) {
			if(is_array($filter)) {
				if(!in_array($key,$filter))
					$this->saveToKeyed($key,$value,$sName);
			}
			else
				$this->saveToKeyed($key,$value,$sName);
		}
	}
	/*
	**	@param	$key	[string]		data array key
	**	@param	$value	[bool | any]	If what will be assigned to session value
	**	@param	$sName	[bool | string]	If string, will save a sub-array using this value
	*/
	private	function saveToKeyed($key,$value = false,$sName = false)
	{
		$value	=	($this->sp_chars)? $this->safe()->encode($value) : $value;

		if($sName) {
			$_SESSION[$sName][$key]	=	$value;
			nApp::call()->saveSetting('_SESSION',array($sName=>array($key=>$value)));
		}
		else
			$this->setSession($key,$value);
	}

	public	function saveSessionArr($array,$value)
	{
		if(is_array($array)) {
			$shift			=	array_shift($array);
			$new[$shift]	=	(!empty($array))? $this->saveSessionArr($array,$value) : $value;
			return $new;
		}
		else
			return $value;
	}

	public	function setSession($var,$value,$reset = true)
	{
		$nApp		=	nApp::call();
		$SESSION	=	$this->toArray($nApp->getSession());

		if(is_array($var)) {
			$shift				=	array_shift($var);
			$settings			=	$this->saveSessionArr($var,$value);
			$SESSION[$shift]	=	$settings;
			if(isset($_SESSION))
				$_SESSION	=	$SESSION;

			$nApp->saveSetting('_SESSION',$SESSION,$reset);
		}
		else {
			# If the session value isn't set already OR isset but needs to be overwritten
			if(!isset($SESSION[$var]) || $reset) {
				if(!is_array($SESSION))
					$SESSION		=	array();

				$SESSION[$var]	=	$value;
			}
			# If is set or reset is false
			else {
				# If the key is already an array
				if(is_array($SESSION[$var])) {
					# Check if there is a numeric for this error
					if(!isset($SESSION[$var][0]))
						# Reset the keys
						$SESSION[$var]	=	array($SESSION[$var],$value);
					else
						$SESSION[$var][]	=	$value;
				}
				else {
					$SESSION[$var]	=	array($SESSION[$var],$value);
				}
			}

			if(isset($_SESSION))
				$_SESSION	=	$SESSION;

			$nApp->saveSetting('_SESSION',$SESSION,$reset);
		}
	}
	/*
	**	@action	Resets the session id number
	*/
	public	function newId($remove = true)
	{
		if(isset($_SESSION))
			session_regenerate_id($remove);
	}
	/*
	**	@action	starts the session
	*/
	public	function start()
	{
		session_start();
	}

	public	function removeSessionKey($array,$find)
	{
		if(!is_array($array))
			return false;

		foreach($array as $key => $value) {
			if(in_array($key,$find))
				continue;

			if(is_array($value)) {
				$new[$key]	=	$this->removeSessionKey($value,$find);
			}
			else {
				$new[$key]	=	$value;
			}
		}

		if(isset($new))
			return $new;
	}
	/*
	**	@action	destroys the session
	*/
	public	function destroy($key = false,$recurse=false)
	{
		$nApp		=	nApp::call();

		if(!empty($key)) {
			$SESSION	=	$nApp->toArray($nApp->getSession());

			if($recurse) {
				if(!is_array($key))
					$key	=	[$key];

				$SESSION	=	$this->removeSessionKey($SESSION,$key);
			}
			else
				unset($SESSION[$key]);

			if(isset($_SESSION))
				$_SESSION	=	(empty($SESSION))? [] : $SESSION;

			$nApp->saveSetting('_SESSION',$SESSION,true);
		}
		else {
			if(isset($_SESSION)) {
				$nApp->saveSetting('_SESSION',array(),true);
				session_destroy();
			}
		}
	}
	/*
	**	@action	stops the session from writing anymore to it
	*/
	public	function lock()
	{
		if(isset($_SESSION))
			session_write_close();

		nApp::call()->saveError('session',array('writeable'=>false));
	}

	public	function isExpired($expireTime)
	{
		$active	=	nApp::call()->getSession('LAST_ACTIVITY');
		return (!empty($active) && (time() - $active > $expireTime));
	}

	public	function setExpired()
	{
		if(!empty(nApp::call()->getSession('username'))) {
			$time	=	time();
			$this->setSession('LAST_ACTIVITY', $time, true);
		}

		return $this;
	}

	public	function toEmpty()
	{
		$nApp		=	nApp::call();
		$nApp->getHelper('NubeData')->destroy('settings','_SESSION');
		$_SESSION	=	array();
		$nApp->saveSetting('_SESSION',array());

		return $this;
	}

	public	function sessionStarted()
	{
		return (session_status() == PHP_SESSION_NONE)? false : true;
	}

	public	function saveAction($actionName = 'action')
	{
		if(!empty($this->getPost($actionName))) {
			$this->setSession($actionName,$this->getPost($actionName),true);
		}
	}
}