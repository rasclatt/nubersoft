<?php
namespace Nubersoft;

class	nConfigEngine extends \Nubersoft\nFunctions
	{
		protected	$table,
					$configs,
					$cEngine,
					$layout,
					$renderKey,
					$useArray,
					$order_by;
		
		public	function __construct(\Nubersoft\configFunctions $configEngine)
			{
				$this->cEngine	=	$configEngine;
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
				$cEngine		=	$this->cEngine;
				if(!empty($this->useArray)) {
					$cEngine->useArray($this->useArray);
				}

				$namesArr	=	$cEngine->getSettings(array($key));
				return (!empty($this->arrayKeys($namesArr)))? $namesArr : false;
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
		
		private	function getActionScripts($file)
			{
				if(is_file($file)) {
					$getLookUp	=	$this->getMatchedArray(array('loadzones','actions'),'',self::call('nRegister')->parseXmlFile($file));
					$locations	=	array();
					$this->extractAll($getLookUp,$locations);
					foreach($locations as $key => $value) {
						$locations[$key]	=	$this->getHelper('nAutomator',$this)->matchFunction($value);
					}
				}
				
				return (!empty($locations))? $locations : array();
			}
		
		public	function getAllActions()
			{
				# Set cache location for actions array
				$actionsCache	=	$this->toSingleDs(nApp::call()->getCacheFolder().DS.'prefs'.DS.'actions.json');
				# If there is a cache file
				if(is_file($actionsCache)) {
					# Get the data and decode it
					$getActions	=	$this->toArray(json_decode(file_get_contents($actionsCache)));
					# If there is a file, save to data and send back
					if(!empty($getActions['actions'])) {
						self::call()->saveSetting('actions',$getActions['actions']);
						return $getActions['actions'];
					}
				}
				# Get the register file to get search locations for files
				$client			=	NBR_CLIENT_SETTINGS.DS.'register'.DS.'config.xml';
				# Get the register file (default)
				$base			=	NBR_SETTINGS.DS.'register'.DS.'config.xml';
				# Merge them so there so there is an accumulated list
				$locations		=	array_merge($this->getActionScripts($base),$this->getActionScripts($client));
				# If there are no locations, then just use some default values (there should be default)
				if(empty($locations)) {
					// Common places to find config.xml files
					$locations[]	=	NBR_ROOT_DIR.'/plugins/';
					$locations[]	=	NBR_SETTINGS.'/actions/';
					$locations[]	=	NBR_CLIENT_DIR.'/plugins/';
					$locations[]	=	NBR_CLIENT_DIR.'/apps/';
					$locations[]	=	NBR_CLIENT_SETTINGS.'/actions/';
				}
				# Create storage array
				$autoloader	=	array();
				# Remove any empty spots in the locations array
				foreach(array_filter($locations) as $config) {
					// Autoload plugins from xml
					if(is_dir($config) || is_file($config)) {
						$confArr		=	$this->getConfig($config);
						if(empty($confArr))
							continue;
						else
							$autoloader	=	array_merge($autoloader,$confArr);
					}
				}
				# Order events
				$this->reorderEvent($this->getEventsList(),$this->getOrderBy('before'),$this->getOrderBy('after'));
				# Save the actions
				$actions['order']	=	$this->getEventsList();
				$actions['actions']	=	$autoloader;
				# Save to data node
				self::call()->saveSetting('actions',$this->toObject($actions));
				# Write actions to file
				self::call('nFileHandler')->writeToFile(array(
					'content'=>json_encode($actions),
					'save_to'=>$actionsCache,
					'overwrite'=>true
				));
				# Save the actions to the data node array
				self::call()->saveSetting('actions',$autoloader);
				# Send back the actions
				return (!empty($autoloader))? $autoloader : false;
			}
		/*
		**	@description	This will reorder the events based on the array. The keys/values must always be before/after
		*/
		protected	function reorderEvent($order,$before,$after)
			{
				$before	=	(empty($before))? array() : $before;
				$after	=	(empty($after))? array() : $after;
				$new	=	array();
				
				foreach($before as $anchor => $find) {
					$anchorRev	=	preg_replace('/[^a-zA-Z\_]/','',$anchor);
				
					$key		=	array_search($find,$order);
					if(isset($order[$key-1]) && $order[$key-1] == $anchor)
						continue;
					
					unset($order[$key]);
					$order		=	array_values($order);
					$placement	=	array_search($anchorRev,$order)+1;
					
					if($placement == 0) {
						$front	=	array_shift($order);
						array_unshift($order,$find);
						$order	=	array_merge(array($front),$order);
					}
					else
						array_splice($order,$placement,0,array($find));
				}
				
				foreach($after as $anchor => $find) {
					$anchor		=	preg_replace('/[^a-zA-Z\_]/','',$anchor);
					# Find the anchor
					$key		=	array_search($find,$order);
					# Find the cut
					$cut		=	array_search($anchor,$order);
					# Remove the after from the array
					unset($order[$cut]);
					$order		=	array_values($order);
					# Insert the after after the value in question
					array_splice($order,($key+1),0,array($anchor));
				}
				
				$this->setOrder($order);
				
				return $this;
			}
		/*
		**	@description	Fetches config files if reference is a valid directory and directory
		**					has xml files in any sub-directory
		**	@param	$pDir	[array|string]	Can be a file path or a directory path
		*/
		protected	function getConfig($pDir)
			{
				# If path is not a directory
				if(!is_dir($pDir)) {
					# If path is not a file, just stop
					if(!is_file($pDir))
						return false;
					else
						# If file, then make add it to array
						$dir['host'][]	=	$pDir;
				}
				# If the file has not been already set to array, fetch recursive
				if(!isset($dir))
					$dir	=	$this->getDirList(array("dir"=>$pDir,"type"=>array("xml")));
				# IF there are no file, stop
				if(empty($dir['host']))
					return false;
				# If there are files, loop through them and parse the xml
				foreach($dir['host'] as $key => $value) {
					$config[]	=	self::call('nRegister')->parseXmlFile($value);
				}
				# Find actions from array
				$configs	=	$this->getMatchedArray(array('action'),'',$config);
				# Create a storage array
				$final		=	array();
				# Flatten all the values from actions
				$this->flattenArray($configs['action'],$final);
				
				if(!isset($final[0]))
					$final	=	array($final);
				
				# Incase there are muliple events with same name, we need to add increment
				# The increment is stripped at the time of ordering
				$i = 0;
				# Loop through those found
				foreach($final as $key => $event) {
					# Get the array that says "event"
					$arr	=	$this->useKeyFromVal($event,array('@attributes','event'));
					# Get the key
					$arrKey	=	key($arr);
					# Get the value of the event
					$action[$arrKey]	=	$arr[$arrKey];
					# Save to the natural order
					$this->order_by['order'][]	=	$arrKey;
					# Assign any positional before requirements
					if(!empty($event['@attributes']['before']))
						$this->order_by['position']['before'][$arrKey.$i]	=	$event['@attributes']['before'];
					# Assign positional after requirements
					if(!empty($event['@attributes']['after']))
						$this->order_by['position']['after'][$arrKey.$i]	=	$event['@attributes']['after'];
					# Auto increment
					$i++;
				}
				# Return the complied action array
				return $action;
			}
		
		public	function getOrderBy($type = false)
			{
				if(!empty($type))
					return (!empty($this->order_by['position'][$type]))? $this->order_by['position'][$type] : false;
				
				return (!empty($this->order_by))? $this->order_by : false;
			}
			
		public	function getEventsList()
			{
				return (!empty($this->order_by['order']))? $this->order_by['order'] : false;
			}
			
		public	function setOrder($array)
			{
				$this->order_by['order']	=	$array;
			}
	}