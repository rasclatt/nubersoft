<?php
namespace Nubersoft;

class	nConfigEngine
	{
		protected	$table,
					$configs,
					$cEngine,
					$layout,
					$renderKey,
					$useArray,
					$nFunc;
		
		public	function __construct(\Nubersoft\configFunctions $configEngine, \Nubersoft\nFunctions $nFunc)
			{
				$this->cEngine	=	$configEngine;
				$this->nFunc	=	$nFunc;
			}
		
		public	function useConfigs($dir = false)
			{
				if(!$dir)
					$dir	=	NBR_CLIENT_DIR;

				$this->configs	=	$this->cEngine	->addLocation($dir)
													->getConfigs();
				return $this;
			}
		
		public	function getConfigs()
			{
				return $this->configs;
			}
		
		public	function useArray($array)
			{
				$this->useArray	=	$array;
				return $this;
			}
		
		public	function hasKey($key)
			{
				$nFunctions		=	$this->nFunc;
				$cEngine		=	$this->cEngine;
				if(!empty($this->useArray)) {
					$cEngine->useArray($this->useArray);
				}

				$namesArr	=	$cEngine->getSettings(array($key));
				return (!empty($nFunctions->arrayKeys($namesArr)))? $namesArr : false;
			}
		
		public	function getLayout()
			{
				return $this->layout;
			}

		protected	function determineInc($includer,$do = false)
			{
				if(isset($includer['require'])) {
					if($do)
						require($do);
					else
						return 'require';
				}
				elseif(isset($includer['require_once'])) {
					if($do)
						require_once($do);
					else
						return 'require_once';
				}
				elseif(isset($includer['include'])) {
					if($do)
						include($do);
					else
						return 'include';
				}
				elseif(isset($includer['include_once'])) {
					if($do)
						include_once($do);
					else
						return 'include_once';
				}
				else
					return false;
			}
			
		public	function getAllConfigs()
			{
				// Common places to find config.xml files
				$locations[]	=	NBR_ROOT_DIR.'/core.plugins/';
				$locations[]	=	NBR_CLIENT_DIR.'/plugins/';
				$locations[]	=	NBR_CLIENT_DIR.'/apps/';
				
				foreach($locations as $config) {
					// Autoload plugins from xml
					if(is_dir($config))
						$autoloader[]	=	$this->getConfig($config);
				}
				
				return (!empty($autoloader))? $autoloader : false;
			}

		protected	function getConfig($pDir,$set = 'plugin')
			{
				AutoloadFunction("get_directory_list");
				if(!is_dir($pDir))
					return false;
				
				$dir	=	get_directory_list(array("dir"=>$pDir,"type"=>array("xml")));
				
				$setRightArr	=	function($array) {
					if(isset($array['onprocessheader']))
						return array('onprocessheader','header');
				};
				
				if(!empty($dir['host'])) {
					// Count how many files to process
					$cConfig		=	count($dir['host']);
					// Loop through configs
					for($i = 0; $i < $cConfig; $i++) {
						// Try and fetch the array from xml
						$cArr		=	\nApp::getRegistry($dir['host'][$i]);
						if(!empty($cArr['onload']))
							\nApp::saveSetting('onload',array($cArr['onload']));
						// Anon function to check for key instruction
						$cArrKey	=	$setRightArr($cArr);
						// If no instructions set skip rest
						if(empty($cArrKey))
							continue;
						// If there is a proper instruction, assign the array
						$arr		=	$cArr[$cArrKey[0]];
						// If no file path is indicated, skip rest
						if(empty($arr['use']))
							continue;
						// Set the requester (post,get,request)
						$requester	=	(!empty($arr['requester']))? $arr['requester'] : 'post';
						// Set the action, if no action, it will just include
						$action		=	(!empty($arr['action']))? $arr['action'] : false;
						// Create a load array
						$autoloader[$cArrKey[1]][$requester][]	=	array("action"=>$action,"include"=>$arr['use']);
					}
				}
				
				return (!empty($autoloader))? $autoloader : false;
			}
	}