<?php
namespace nWordpress\Automator;

use \Nubersoft\nApp as nApp;

class Observer extends \nWordpress\Automator implements \Nubersoft\nObserver
{
	private	$nApp;
	
	public	function __construct()
	{
		$this->nApp	=	nApp::call();
	}
	
	public	function listen()
	{
		$nReflect	=	$this->nApp->getHelper('nReflect');
		$args		=	func_get_args();
		$type		=	(!empty($args[0]))? $args[0].'.xml' : 'init.xml';
		$lookup		=	NBR_CLIENT_DIR.DS.'settings'.DS.'actions'.DS.$type;
		
		# Stop if an action isn't even set
		if(empty($this->getRequest('action')))
		   return false;
		
		if(!is_file($lookup))
			return false;
		# Fetch the xml and convert to array
		$xml	=	$this->nApp->getHelper('nRegister')->parseXmlFile($lookup);
		# Create array from string if required
		$action	=	(is_array($xml['action']) && isset($xml['action'][0]))? $xml['action'] : [$xml['action']];
		# Loop through each action
		foreach($action as $key => $act) {
			# Remove commments, they are stupid
			if(isset($act['comment']))
				unset($act['comment']);
			# Set some requirements
			# Check that this action has a name
			$aName			=	(isset($act['@attributes']['name']))? strtolower($act['@attributes']['name']) : false;
			# Check if the request is supposed to be ajax-based
			$isAjax			=	(isset($act['@attributes']['is_ajax']))? strtolower($act['@attributes']['is_ajax']) : false;
			# Check what kind of request is being required ($_POST is default)
			$requestType	=	(isset($act['@attributes']['request']))? strtolower($act['@attributes']['request']) : 'post';
			# Turn boolean from string "true" or "false"
			if(!empty($isAjax))
				$isAjax	=	(strtolower($isAjax) == 'true');
			# If no action name, skip action
			if(empty($aName))
				continue;
			# If ajax is required for request, stop if not ajax request
			if($isAjax && !$this->isAjaxRequest())
				continue;
			# Skip if request type doesn't match
			if($requestType == 'post'){
				if($this->getPost('action') != $aName)
					continue;
			}
			elseif($requestType == 'get') {
				if($this->getGet('action') != $aName)
					continue;
			}
			elseif($requestType == 'request') {
				if($this->getRequest('action') != $aName)
					continue;
			}
			else {
				# Just stop if the request doesn't match
				if($aName != $this->getRequest('action'))
					continue;
			}
			# Reset the named array
			$action[$aName]	=	$act;
			# Removed unnamed array
			unset($action[$act['@attributes']['name']]['@attributes']);
			unset($action[$key]);
			# Run any requested functions
			if(isset($action[$aName]['function'])) {
				# Create numbered array if only one function
				if(!isset($action[$aName]['function'][0]))
					$action[$aName]['function']	=	[$action[$aName]['function']];
				# Loop functions
				foreach($action[$aName]['function'] as $kfo => $funcObj) {
					# Set name
					$func	=	$funcObj['name'];
					# Don't run if not already loaded
					if(!function_exists($func))
						continue;
					# Check for injected items
					if(isset($funcObj['inject'])) {
						if(is_string($funcObj['inject']))
							$funcObj['inject']	=	[$funcObj['inject']];

						$inj	=	[];
						foreach($funcObj['inject'] as $kInj => $injector) {
							if(stripos($injector,'~') !== false) {
								$inj[$kInj]	=	$this->automate($injector);
							}
						}
						//$nReflect
						$func(...$inj);
					}
					else {
						$func();
					}
				}
			}
			
			if(isset($action[$aName]['construct'])) {
				if(!isset($action[$aName]['construct'][0]))
					$action[$aName]['construct']	=	[$action[$aName]['construct']];
				
				foreach($action[$aName]['construct'] as $key => $constr) {
					switch($constr['name']) {
						case('exit'):
							exit;
						case('return'):
							return;
						case('die'):
							die(((isset($constr['value']))? $constr['value'] : ''));
					}
				}
			}
			
			if(isset($action[$aName]['class'])) {
				$this->doClassWorkflow($action[$aName],$nReflect);
			}
		}
	}
	
	protected	function doClassWorkflow($action, \Nubersoft\nReflect $nReflect)
	{
		if(isset($action['class'])) {
			if(!isset($action['class'][0]))
				$action['class']	=	[$action['class']];

			foreach($action['class'] as $key => $constr) {
				$method	=	(!empty($constr['method']))? $constr['method'] : [];

				if(!is_array($method))
					$method	=	[$method];

				if(empty($method)) {
					$nReflect->execute($constr['name']);
					continue;
				}

				foreach($method as $method) {
					$nReflect->reflectClassMethod($constr['name'],$method);
				}
			}
		}
	}
	
	public	function loadEvents($type)
	{
		$path	=	NBR_CLIENT_DIR.DS.'settings'.DS.'workflows'.DS.$type.'.xml';
		
		if(!is_file($path))
			return false;
		
		# Fetch the xml and convert to array
		$xml	=	$this->nApp->getHelper('nRegister')->parseXmlFile($path);
		
		if(empty($xml['event']))
			return false;
		
		if(!isset($xml['event'][0]))
			$xml['event']	=	[$xml['event']];
		
		foreach($xml['event'] as $event) {
			
			if(empty($event['@attributes']['name']))
				continue;
			
			$actName	=	$event['@attributes']['name'];
			
			if(empty($event['function']['name']))
				continue;
			
			$func	=	$event['function']['name'];
			
			add_action($actName, $func);
		}
	}
}