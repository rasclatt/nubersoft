<?php
namespace Nubersoft;

class	nAutoloadAsset
	{
		private	static	$singleton;
		private	$dir,
				$fPath,
				$fcPath;
		
		public	function __construct()
			{
				if(empty(self::$singleton))
					self::$singleton	=	$this;
					
				return self::$singleton;
			}
		
		public function stripPath($classname)
			{
				$pathwork		=	explode("\\",trim($classname,'\\'));
				$pathwork		=	array_filter($pathwork);
				$filename		=	array_pop($pathwork);
				$path			=	$pathwork;
				// Strip namespace to path
				$this->fPath	=	implode(_DS_,$path)._DS_.$filename.'.php';
				$this->fcPath	=	$this->fPath;
				return $this;
			}
		
		public	function useLocation($dir)
			{
				$this->dir	=	$dir;
				return $this;
			}
		
		public	function loadConfigs()
			{
				// Create Config Engine
				$nConfig	=	new \Nubersoft\configFunctions(new \Nubersoft\nAutomator());
				// Get configs from client folder
				$configs	=	$nConfig->addLocation($this->dir)->getConfigs();
				if(!empty($configs))
					\nApp::saveSetting('configs',$configs);
				
				return $configs;
			}
		
		public	function loadClass($configs,$nConfig)
			{
				return $this->loadByType(array('register','class'),$configs,$nConfig);
			}
			
		public	function loadNamespace($configs,$nConfig)
			{		
				return $this->loadByType(array('register','namespace'),$configs,$nConfig);
			}
		
		private	function loadByType($typeArray,$configs,$nConfig)
			{
				if(empty($configs))
					return false;
					
				return $nConfig->useArray($configs)->getSettings($typeArray);
			}
		
		public	function findAndLoad($array)
			{
				if(!is_array($array))
					return false;
				
				foreach($array as $rawPath) {
					$pathRaw	=	\nApp::nAutomator()->matchFunction($rawPath);
					$includer 	=	str_replace(_DS_._DS_,_DS_,$pathRaw._DS_.$this->fPath);
					if(is_file($includer)) {
						require_once($includer);
						return;
					}
				}
			}
		
		public	function autoload($classname)
			{
				// Create Config Engine
				$nConfig	=	new \Nubersoft\configFunctions(new \Nubersoft\nAutomator());
				// Fetch the config array
				$configs	=	$this->loadConfigs();
				// Filter out class locations
				$classes	=	$this->loadClass($configs,$nConfig);
				// Filter out namespaces locations
				$namespaces	=	$this->loadNamespace($configs,$nConfig);
				// Filter class array
				$classes	=	(is_array($classes) && !empty($classes['class']))? array_unique($classes['class']) : array();
				$namespaces	=	(is_array($namespaces) && !empty($namespaces['namespace']))? array_unique($namespaces['namespace']) : array();
				$merge		=	array_filter(array_merge($classes,$namespaces));
				// Run through the array and see if it matches
				if(!empty($merge))
					$this->findAndLoad($merge,str_replace('\\',_DS_,$classname));
			}
		
		public	function getPaths()
			{
				return (object) array('raw_path'=>$this->fPath,'class_path'=>$this->fcPath);
			}
	}