<?php
namespace Nubersoft;

class configFunctions extends \Nubersoft\nFunctions
	{
		private	static	$configs	=	array();
		private	$has,
				$altArray;
		
		public	function __construct(\Nubersoft\nAutomator $nAutomator)
			{
				# Fetch cached config documents
				# This will return the list of parsed configs as well as the parsed array
				$nAutomator->getCachedConfigs()->toPrefs();
				
				return parent::__construct();
			}
		/*
		**	@description	This function will add a directory to search in for config files
		**	@param			$dir [string] This is the path to search in. Path is recursive, so searches sub folders
		*/
		public	function addLocation($dir)
			{
				# Parse the files inside this directory
				$lConfigs		=	nApp::call('nRegister')->parseRegFile($dir);
				self::$configs	=	$this->toArray(nApp::call()->getDataNode('configs'));
				# Return for method chaining
				return $this;
			}
		/*
		**	@description	Simply returns the private configs array
		*/
		public	function getConfigsArr()
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
				$useArray	=	$this->toArray($useArray);	
				# Set default
				$available	=	false;
				# If input is a string, try splitting
				if(is_string($val))
					$pathway	=	(strpos($val,$split) !== false)? explode($split,$val) : array($val);
				# If already an array just assign
				elseif(is_array($val))
					$pathway	=	$val;
				# Just return all configs
				elseif(empty($val))
					return $this->getConfigsArr();
				# Recurse through the config array to find the proper array list
				if(!empty($pathway) && is_array($pathway))
					$available	=	$this->findByKeyOrder($useArray,$pathway)->getKeyList();
				
				# Return array or bool
				return $available;
			}
		
		public	function useArray($array)
			{
				$this->altArray	=	$array;
				
				return $this;
			}
	}