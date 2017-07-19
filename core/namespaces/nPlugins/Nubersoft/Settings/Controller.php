<?php
namespace nPlugins\Nubersoft\Settings;

class Controller extends \Nubersoft\nApp
	{
		/*
		**	@description	Retrieve the reg file specifically
		*/
		public	function getRegistry($key = false)
			{
				$Cache	=	$this->nCache();
				# See if stored in the load
				if(!empty($this->getDataNode('registry')) && !empty($this->getCacheFolder()) && $Cache->allowCacheRead()) {
					$reg	=	$this->toArray($this->getDataNode('registry'));
					if(!empty($key))
						return (!empty($reg[$key]))? $reg[$key] : false;
					
					return $reg;
				}
				# Cached file for registry
				$cache	=	$this->toSingleDs($this->getCacheFolder().DS.'registry.json');
				# If there is a registry cached
				if(is_file($cache) && $Cache->allowCacheRead()) {
					# Decode and send back
					$decode	=	json_decode(file_get_contents($cache),true);
					# Save to settings
					if(empty($this->getDataNode('registry')))
						$this->saveSetting('registry',$decode);
					if($key)
						return (isset($decode[$key]))? $decode[$key] : false;
					return $decode;
				}
				# Default location for registry file
				$file	=	NBR_CLIENT_DIR.DS.'settings'.DS.'registry.xml';
				# If not found
				if(!is_file($file)) {
					# Let it be known
					throw new nException('No registry found.',404001);
					return false;
				}
				# Parse the xml
				$reg	=	$this->getHelper('nRegister')->parseXmlFile($file);
				# Set the default path to the cache folder
				$cDir	=	str_replace(NBR_ROOT_DIR,'',NBR_CLIENT_DIR).DS.'settings'.DS.'cachefiles';
				# If reg is parsed
				if(!empty($reg)) {
					# If there is a previous define
					if(defined('CACHE_DIR'))
						$cDir	=	CACHE_DIR;
					else {
						# Try and extract cache dir from reg file
						$cDirFind	=	$this->getMatchedArray(array('ondefine','cache_dir'),false,$reg);
						# If there is a cache folder, assign it
						if(!empty($cDirFind['cache_dir'][0]))
							$cDir	=	$cDirFind['cache_dir'][0];
					}
				}
				# Build the path to cache
				$cDir	=	NBR_ROOT_DIR.DS.trim($cDir,DS).DS;
				# Strip out any double forward slashes
				$cache	=	$this->toSingleDs($cDir.DS.'registry.json');
				# If not empty
				if(!empty($reg)) {
					$opts	=	array(
									'content'=>json_encode($reg),
									'save_to'=>$cache,
									'secure'=>true,
									'overwrite'=>true
								);
					# Save to disk as json
					if($Cache->allowCacheRead() && !$this->isAjaxRequest())
						$this->getHelper('nFileHandler')->writeTofile($opts);
					# Save to settings
					$this->saveSetting('registry',$reg);
					# Return
					$reg	=	$this->toArray($this->getDataNode('registry'));
					if(!empty($key))
						return (!empty($reg))? $reg[$key] : false;
					
					return $reg;
				}
			}
		/*
		**	@description	This method gets the paths to search out for new config files
		**	@param	[object] This will be an instance of a parser class (nRegister() is default)
		*/
		public	function getLoadZones($xmlParser = false)
			{
				# See if this node is available
				$loadZones		=	$this->getDataNode('loadzones');
				# If available, return it
				if(!empty($loadZones))
					return $this->toArray($loadZones);
				# Assign Parser
				$xmlParser		=	(is_object($xmlParser))? $xmlParser : $this->getHelper('nRegister');
				# Common zone file path
				$zoneFilePath	=	'settings'.DS."register".DS.'config.xml';
				# Try parsing the core loadzone
				$regfile		=	NBR_CORE.DS.$zoneFilePath;
				# Client loadzone
				$cRegfile		=	NBR_CLIENT_DIR.DS.$zoneFilePath;
				# Parse xml
				$loadZoneNbr	=	$xmlParser->parseXmlFile($regfile);
				# Try and parse client loadzone
				$loadZoneClient	=	(is_file($cRegfile))? $xmlParser->parseXmlFile($cRegfile) : false;
				# Create instance of nFunctions()
				$nFunc			=	$this;
				# Create a parsing function for strings returned by xml
				$combineConf	=	function($array,&$new) use ($nFunc)
					{
						$zones	=	array_keys($array['loadzones']);
						$nAuto	=	new \Nubersoft\nAutomator($this);
						foreach($zones as $zone) {
							$new[$zone]	=	$nFunc->getMatchedArray(array('loadzones',$zone),'_',$array);
							foreach($new[$zone][$zone] as $val) {
								if(is_array($val)) {
									foreach($val as $subVal) {
										$string[$zone][]	=	$subVal;
									}
								}
								else
									$string[$zone][]	=	$val;
							}
							
							if(isset($string[$zone]))
								$new[$zone][$zone]	=	$string[$zone];
							
							if(!empty($new[$zone][$zone])) {
								$packed		=	array_map(function($v) use ($nAuto) {
									return $nAuto->matchFunction($v);
								},$new[$zone][$zone]);
								
								$new[$zone]	=	$packed;
							}
						}
					};
				$core	=	
				$client	=	array();
				$combineConf($loadZoneNbr,$core);
				if(!empty($loadZoneClient)) {
					$combineConf($loadZoneClient,$client);
				}
				# Get unique categories from both client and core arrays
				$looper	=	array_unique(array_merge(array_keys($core),array_keys($client)));
				# Set a final array to store the paths
				$final	=	array();
				foreach($looper as $title) {
					if(isset($core[$title])) {
						if(isset($client[$title]))
							$final[$title]	=	array_merge($core[$title],$client[$title]);
						else
							$final[$title]	=	$core[$title];
					}
					elseif(isset($client[$title])) {
						$final[$title]	=	$client[$title];
					}
					
					if(isset($final[$title]))
						$final[$title]	=	array_unique($final[$title]);
				}
				# Check if there is a cache pause on
				$allow		=	$this->nCache()->allowCacheRead();
				# Allow saving if no cache pause is active and request is not ajax
				if($allow && !$this->isAjaxRequest()) {
					# Save to file
					$this->savePrefFile('loadzones',$final);
					# Save loadzones to data array
					$this->saveSetting('loadzones',$final);
				}
				# Return the values
				return $final;
			}
			
		public	function getConfigs()
			{
				# Get args
				$args		=	func_get_args();
				# Set the location for the file
				$location	=	(!empty($args[0]))? $args[0] : false;
				# Fetch the paths of where configs can load from
				$zones		=	$this->getLoadZones();
				# Get the configs parser
				$parser		=	new \Nubersoft\configFunctions(new \Nubersoft\nAutomator($this));
				# If there are pages to loop through
				if(!empty($zones)) {
					foreach($zones as $title => $zoneArr) {
						# If a loadzone is empty, skip
						if(empty($zoneArr))
							continue;
						# If a set of data is available, loop through it
						foreach($zoneArr as $loadspots) {
							$parser	->addLocation($loadspots);
						}
					}
				}
				# This setting allows for the addition of new search locations
				if(is_array($location)) {
					//Loop through array and load
					foreach($location as $load) {
						$parser	->addLocation($this->getHelper('nAutomator',$this)->matchFunction($load));
					}
				}
				# Parse and fetch xml array
				$regFiles	=	$parser->getConfigsArr();
				return (is_array($regFiles))? $regFiles : $regFiles;
			}
		/*
		**	@description	Wrapper for checking if production
		*/
		public	function isLiveMode()
			{
				return ($this->getServerMode() == 'prod');
			}
		/*
		**	@description	Wrapper for checking if developer mode
		*/
		public	function isDevMode()
			{
				return ($this->getServerMode() == 'dev');
			}
		/*
		**	@description	Fetches modes from the user config or registry if set
		**	@returns		Returns either the contstant's value or default value ($default)
		*/
		public	function getMode($type,$default,$fromConfig = false)
			{
				$raw	=	strtolower($type.'_mode');
				# Build constant
				$const	=	strtoupper($raw);
				# If only required to look in config file, send back response
				if(!$fromConfig)
					return (defined($const))? constant($const) : $default;
				# Fetch from the config file
				$mData	=	$this->getMatchedArray(array('ondefine',$raw));
				
				if(!empty($mData[$raw][0]) && !is_array($mData[$raw][0]))
					return $mData[$raw][0];
				
				return $default;
			}
		/*
		**	@description	Wrapper for getting the current error mode
		*/
		public	function getErrorMode($fromConfig = false)
			{
				return $this->getMode('error','E_ALL',$fromConfig);
			}
		/*
		**	@description	Wrapper for getting the current server mode
		*/
		public	function getServerMode($def = 'live',$fromConfig = false)
			{
				# Get the server mode from config
				$serverMode	=	strtolower($this->getMode('server',$def,$fromConfig));
				# Live possibilities
				$live		=	array('live','production','prod','p','true',true);
				# Send back standard response
				return (in_array($serverMode,$live))? 'prod' : 'dev';
			}
	}