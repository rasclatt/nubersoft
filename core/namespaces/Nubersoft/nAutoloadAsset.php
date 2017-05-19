<?php
namespace Nubersoft;

class	nAutoloadAsset extends \Nubersoft\nApp
	{
		private	$dir,
				$fPath,
				$fcPath;
			
		public function stripPath($classname)
			{
				$pathwork		=	explode("\\",trim($classname,'\\'));
				$pathwork		=	array_filter($pathwork);
				$filename		=	array_pop($pathwork);
				$path			=	$pathwork;
				# Strip namespace to path
				$this->fPath	=	implode(DS,$path).DS.$filename.'.php';
				$this->fcPath	=	$this->fPath;
				return $this;
			}
		
		public	function useLocation($dir)
			{
				$this->dir	=	$dir;
				return $this;
			}
		
		public	function loadConfigs(\Nubersoft\configFunctions $nConfig)
			{
				# Get configs from client folder
				$configs	=	$nConfig->addLocation($this->dir)->getConfigsArr();
				if(!empty($configs) && empty($this->getDataNode('configs')))
					$this->saveSetting('configs',$configs);
				
				return $configs;
			}
		
		public	function loadClass($configs,$nConfig)
			{
				return $this->filterLoadArray($configs,$nConfig,'class');
			}
			
		public	function loadNamespace($configs,$nConfig)
			{	
				return $this->filterLoadArray($configs,$nConfig,'namespace');
			}
		
		private	function filterLoadArray($configs,$nConfig,$key)
			{
				$nAutomator	=	$this->getHelper('nAutomator',$this);
				$gathered	=	array();
				$namespaces	=	$this->loadByType(array('register',$key),$configs,$nConfig);
				if(isset($namespaces[$key]) && !empty($namespaces[$key]))
					$this->extractAll($namespaces[$key],$gathered);
				
				return (is_array($gathered))? array_unique(array_map(function($v) use ($nAutomator) {
						return $nAutomator->matchFunction(rtrim($v,'/'));
					},$gathered)) : array();
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
				
				foreach($array as $path) {
					# If there is a user defined function/constant, check it
					if(strpos($path,'~') !== false)
						$path	=	$this->getHelper('nAutomator',$this)->matchFunction($path);
					
					$includer 	=	str_replace(DS.DS,DS,$path.DS.$this->fPath);
					if(is_file($includer)) {
						require_once($includer);
						return;
					}
				}
			}
		
		public	function autoloadClass($classname)
			{
				$nAutoloadAssets	=	$this;
				# Save namespaces to file, it's expensive to search for them all the time
				$namepaces	=	$this->getPrefFile('namespace_list', array('save'=>true), false, function($path,$nApp) use ($nAutoloadAssets) {
					# Create Config Engine
					$nConfig	=	$nAutoloadAssets->getHelper('configFunctions',$nApp->getHelper('nAutomator',$nApp));
					# Fetch the config array
					$configs	=	$nAutoloadAssets->loadConfigs($nConfig);
					# Filter out class locations
					$classes	=	$nAutoloadAssets->loadClass($configs,$nConfig);
					# Filter out namespaces locations
					$namespaces	=	$nAutoloadAssets->loadNamespace($configs,$nConfig);
					# Combine both arrays to create a super namespace array
					$array		=	array_filter(array_merge($classes,$namespaces));
					# Process the paths
					if(empty($array))
						return false;
					# Create nAutomator instance
					$nAutomator	=	$nApp->getHelper('nAutomator',$nApp);
					foreach($array as $rawPath)
						$classPath[]	=	$nAutomator->matchFunction($rawPath);
					# Send class path
					return (!empty($classPath))? $classPath : false;
				});
				# Run through the array and see if it matches
				if(!empty($namepaces))
					$this->findAndLoad($namepaces);
			}
		
		public	function getPaths()
			{
				return (object) array('raw_path'=>$this->fPath,'class_path'=>$this->fcPath);
			}
	}