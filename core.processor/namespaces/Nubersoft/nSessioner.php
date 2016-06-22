<?php
namespace Nubersoft;

class	nSessioner
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
			}
		/*
		**	@param	$array	[array]			Requires the associate data array
		**	@param	$filter	[bool | array]	If array, check that data array key(s) are in filter array. Set if not
		**	@param	$sName	[bool | string]	If string, will save a sub-array using this value
		*/
		public	function makeSession($array,$filter = false,$sName = false)
			{
				foreach($array as $key => $value) {
					if(is_array($fliter)) {
						if(!in_array($key,$fliter)) {
							$this->saveToKeyed($key,$value,$sName);
						}
					}
				}
			}
		/*
		**	@param	$key	[string]		data array key
		**	@param	$value	[bool | any]	If what will be assigned to session value
		**	@param	$sName	[bool | string]	If string, will save a sub-array using this value
		*/
		private	function saveToKeyed($key,$value = false,$sName = false)
			{
				$value	=	($this->sp_chars)? \Safe::encode($value) : $value;
				
				if($sName)
					$_SESSION[$sName][$key]	=	$value;
				else
					$_SESSION[$key]	=	$value;
			}
		/*
		**	@action	Resets the session id number
		*/
		public	function newId($remove_old = true)
			{
				session_regenerate_id($remove_old);
			}
		/*
		**	@action	starts the session
		*/
		public	function start()
			{
				session_start();
			}
		/*
		**	@action	destroys the session
		*/
		public	function destroy()
			{
				session_destroy();
			}
		/*
		**	@action	stops the session from writing anymore to it
		*/
		public	function lock()
			{
				session_write_close();
				\nApp::saveError('session',array('writeable'=>false));
			}
	}