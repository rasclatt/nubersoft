<?php
namespace Nubersoft;

class configFunctions
	{
		private	static	$configs	=	array();
		private	$has,
				$altArray;
		private static	$singleton;
		
		public	function __construct(\Nubersoft\nAutomator $nAutomator)
			{
				if(!empty(self::$singleton))
					return self::$singleton;
				
				self::$singleton	=	$this;
				// Fetch cached config documents
				// This will return the list of parsed configs as well as the parsed array
				$nAutomator->getCachedConfigs()->toPrefs();
				return self::$singleton;
			}
		/*
		**	@description	This function will add a directory to search in for config files
		**	@param			$dir [string] This is the path to search in. Path is recursive, so searches sub folders
		*/
		public	function addLocation($dir)
			{
				// Parse the files inside this directory
				$lConfigs	=	\nApp::nRegister()->parseRegFile($dir);
				// If there are config files
				if(is_array($lConfigs) && !empty($lConfigs)) {
					// Add them to existing configs
					self::$configs	=	array_merge(self::$configs,$lConfigs);
				}
				
				if(is_array(self::$configs)) {
					foreach(self::$configs as $obj) {
						$filter[]	=	json_encode($obj);
					}
					
					$rev			=	array_unique($filter);
					self::$configs	=	array();
					foreach($rev as $arr) {
						self::$configs[]	=	json_decode($arr,true);
					}
				}
				// Return for method chaining
				return $this;
			}
		/*
		**	@description	Simply returns the private configs array
		*/
		public	function getConfigs()
			{
				return self::$configs;
			}
		/*
		**	@description	Recursively search through array for specific key order
		**	@param	$val [bool,array,string]	FALSE returns config array, array designates search order, string splits to array
		*/
		public	function getSettings($val = false,$split = '_')
			{
				$useArray	=	(!empty($this->altArray))? $this->altArray : self::$configs;	
				$useArray	=	\nApp::nFunc()->toArray($useArray);	
				// Set default
				$available	=	false;
				// If input is a string, try splitting
				if(is_string($val))
					$pathway	=	(strpos($val,$split) !== false)? explode($split,$val) : array($val);
				// If already an array just assign
				elseif(is_array($val))
					$pathway	=	$val;
				// Just return all configs
				elseif(empty($val))
					return $this->getConfigs();
				// Recurse through the config array to find the proper array list
				if(!empty($pathway) && is_array($pathway)) {
					$available	=	\nApp::nFunc()->findByKeyOrder($useArray,$pathway)->getKeyList();
				}
				// Return array or bool
				return $available;
			}
		
		public	function useArray($array)
			{
				$this->altArray	=	$array;
				
				return $this;
			}
	}