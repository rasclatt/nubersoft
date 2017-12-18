<?php
namespace Nubersoft\nFileHandler;

use \Nubersoft\nFileHandler as File;

class Model extends \Nubersoft\nApp
{
	public	function getPrefFile($name,$settings = false,$raw = false,$callback = false)
	{
		$named			=	(!empty($settings['node']))? $settings['node'] : false;
		$pref_name		=	(!empty($settings['name']))? $settings['name'] : $name;
		$save			=	(!empty($settings['save']))? $settings['save'] : false;
		$parseLocation	=	(!empty($settings['xml']))? $settings['xml'] : false;
		$type			=	(!empty($settings['type']))? $settings['type'] : 'json';
		$matched		=	(!empty($settings['match']))? $settings['match'] : false;
		$limit			=	(!empty($settings['limit']))? $settings['limit'] : false;
		$pre_proc		=	(isset($settings['preprocess']))? $settings['preprocess'] : true;
		$reset			=	(isset($settings['reset']))? $settings['reset'] : true;
		$prefFile		=	($raw)? $name : $this->toSingleDs($this->getCacheFolder().DS.'prefs'.DS.$pref_name.'.'.$type);
		$Cache			=	$this->nCache();

		if(is_file($prefFile) && $Cache->allowCacheRead()) {
			$cont	=	json_decode(file_get_contents($prefFile),true);
			if($reset) {
				if(!empty($cont))
					return $cont;
			}
			else
				return $cont;
		}
		# If there is no directory set
		if(empty($parseLocation)) {
			if(is_callable($callback))
				$config	=	$callback($prefFile,$this);
			else
				return false;
		}
		else
			$parseFile		=	$this->toSingleDs($parseLocation.DS.$name.'.xml');

		if(!empty($parseFile) && is_file($parseFile)) {
			if(is_callable($callback)) {
				$config	=	$callback($parseFile,$this);
			}
			else {
				$config	=	$this->toArray($this->getHelper('nRegister')->parseXmlFile($parseFile));
				# If there is a matched array
				if(!empty($matched)) {
					# Retieve an array from the main array
					$matchArr	=	$this->getMatchedArray($matched,'_',$config);
					# Jump to the end of the search array
					end($matched);
					# Get the key
					$getLastKey	=	key($matched);
					# Get the last value from search array
					$lastKey	=	$matched[$getLastKey];
					# If there is a valid array do more to whittle it down
					if(!empty($matchArr[$lastKey])) {
						if($limit) {
							if($limit == 1) 
								$config	=	(isset($matchArr[$lastKey][0]))? $matchArr[$lastKey][0] : false;
							else {
								for($i = 0; $i < $limit; $i++)
									$config[$i]	=	$matchArr[$lastKey][$i];
							}
						}
						else
							$config	=	$matchArr[$lastKey];
					}
					else
						$config	=	false;
				}
			}
		}

		if(!isset($config))
			return array();

		if(is_array($config) && $pre_proc) {
			$nApp	=	$this;
			$config	=	$this->arrayWalkRecursive($config,function($value) use ($nApp) {
				$v	=	$nApp->getHelper('nAutomator',$this)->matchFunction($nApp->getBoolVal($value));
				return $v;
			});
		}

		if($save && !$this->isAjaxRequest() && $Cache->allowCacheRead() && !empty($config)) {
			$this->savePrefFile($pref_name,$config);
		}

		if($named) { // && !$this->isAjaxRequest() && $Cache->allowCacheRead()
			$this->saveSetting($named,$config);
		}

		return $config;
	}
}