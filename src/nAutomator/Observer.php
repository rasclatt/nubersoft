<?php
namespace Nubersoft\nAutomator;

class Observer extends \Nubersoft\nAutomator implements \Nubersoft\nObserver
{
	use \Nubersoft\nUser\enMasse;
	use \Nubersoft\Settings\enMasse;
	
	protected	$config;
	protected	$actionName	=	'action';
	
	public	function listen()
	{
		# Normalize the config array
		$array	=	$this->normalizeWorkflowArray($this->config);
		
		$this->doWorkflow($array);
	}
	
	public	function setFlow($value, $type = 'work')
	{
		$method			=	ucfirst($type);
		$this->config	=	$this->{"getClient{$method}flow"}($value);
		if(empty($this->config)) {
			$this->config	=	$this->{"getSystem{$method}flow"}($value);
		}
		else {
			$this->config	=	array_merge($this->{"getSystem{$method}flow"}($value), $this->config);
		}
		
		return $this;
	}
	
	public	function setWorkflow($value)
	{
		$this->setFlow($value);
		return $this;
	}
	
	public	function setBlokflow($value)
	{
		$this->setFlow($value, 'block');
		return $this;
	}
	
	public	function setActionKey($value)
	{
		$this->actionName	=	$value;
		return $this;
	}
	
	protected	function getFlowFromDb($kind)
	{
		$flow	=	$this->getComponentBy(['component_type' => 'plugin_'.$kind, 'page_live' => 'on'],'=','AND','content');
		if(!empty($flow)) {
			$flow	=	trim(implode(array_map(function($v){
				return \Nubersoft\nApp::call()->dec($v['content']);
			}, $flow), ''));
		}
		
		return (!empty($flow) && is_string($flow))? $flow : false;
	}
	
	protected	function determineXmlType($xml, $kind)
	{
		return ($kind == 'db')? $this->toArray(simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><config>'.$xml.'</config>')) : $this->toArray(simplexml_load_file($xml));
	}
	
	public	function runBlockflow()
	{
		$templates	=	$this->getDataNode('templates');
		$args		=	func_get_args();
		$file		=	$args[0].'.xml';
		$dir		=	'settings'.DS.'blockflows';
		$actdir		=	'settings'.DS.'actions';
		$pgpath		=	(is_dir($templates['paths']['page'])); 
		$blocks		=	[
			'page' => (!empty($templates['paths']['page']) && $pgpath)? str_replace(DS.DS,DS,$templates['paths']['page'].DS.$dir.DS.$file)  : false,
			'client' => NBR_CLIENT_SETTINGS.DS.'blockflows'.DS.$file,
			'site' => (!empty($templates['paths']['site']))? str_replace(DS.DS,DS,$templates['paths']['site'].DS.$dir.DS.$file) : false,
			'default' => NBR_SETTINGS.DS.'blockflows'.DS.$file,
			'db' => $this->getFlowFromDb('blockflows')
		];
		$actionSets['object']	=
		$actionStore['object']	=
		$storage['object']	=	[];
		$templates	=	array_filter($templates);
		$allowReq	=	array_filter([
			'get' => $this->getGet($this->actionName),
			'post' => $this->getPost($this->actionName)
		]);
		if(!empty($allowReq)) {
			
			$actions	=	array_filter(array_unique([
				'default' => str_replace(DS.DS, DS, NBR_CORE.DS.$actdir.DS.$file),
				'site' => (!empty($templates['paths']['site']))? str_replace(DS.DS,DS,$templates['paths']['site'].DS.'core'.DS.$actdir.DS.$file) : false,
				'client' => NBR_CLIENT_SETTINGS.DS.'actions'.DS.$file,
				'page' => (!empty($templates['paths']['page']))? str_replace(DS.DS,DS,$templates['paths']['page'].DS.$actdir.DS.$file) : false,
				'db' => $this->getFlowFromDb('action')
			]));
			
			foreach($actions as $kind => $actObj) {
				# Stop if nothing set
				if(empty($actObj))
					continue;
				# Stop if invalid
				if($kind != 'db' && !is_file($actObj))
					continue;
				# Convert to xml from either from a file or string
				$arr			=	$this->determineXmlType($actObj, $kind);
				# Re-jigger the xml array
				$actionStore	=	$this->normalizeWorkflowArray(array_merge($actionStore['object'],$arr));
				# If there are objects to processes, do so
				if(!empty($actionStore['object'])){
					foreach($actionStore['object'] as $acevent => $actobj) {
						if(strpos($acevent, ',') !== false) {
							$events_exp	=	array_filter(array_map('trim',explode(',',$acevent)));
							
							if(!in_array($this->getPost($this->actionName), $events_exp) && !in_array($this->getGet($this->actionName), $events_exp)) {
								unset($actionStore['object'][$acevent]);
							}
						}
						else {	
							if(!in_array($acevent, $allowReq)) {
								unset($actionStore['object'][$acevent]);
							}
						}
					}
				}
				# Merge from different spots
				if(!empty($actionStore['object']))
					$actionSets['object']	=	array_merge($actionSets['object'], $actionStore['object']);
			}
		}
		# If actions, reassign to base
		if(!empty($actionSets['object']))
			$actionStore['object']	=	$actionSets['object'];
		# Remove empty config paths then reverse to set the priority of the events
		$blocks	=	array_reverse(array_filter(array_unique($blocks)));
		# Loop block flows
		foreach($blocks as $kind => $config) {
			# Stop if empty (shouldn't be empty at this point)
			if(empty($config))
				continue;
			# Stop if invalid
			if($kind != 'db' && !is_file($config))
				continue;
			# Convert to xml from either from a file or string
			$arr	=	$this->determineXmlType($config, $kind);
			# Get the config data
			$bArr	=	$this->normalizeWorkflowArray(array_merge($storage['object'], $arr));
			# If there is already a stored file
			if(!empty($storage['object']))
				# combine with new
				$storage['object']	=	array_merge($storage['object'], $bArr['object']);
			else
				# Assign
				$storage	=	$bArr;
		}
		
		$obj	=	(!empty($actionStore['object']))? array_merge($actionStore['object'], $storage['object']) : $storage['object'];
		$new	=	[];
		foreach($obj as $event => $details) {
			$name	=	(strpos($event, ',') !== false)? array_filter(array_map('trim', explode(',', $event))) : $event;
			if(is_array($name)) {
				$count	=	count($name);
				for($i = 0; $i < $count; $i++) {
					if($this->getPost($this->actionName) == $name[$i] || $this->getGet($this->actionName) == $name[$i]) {
						$new[$name[$i]]			=	$details;
						$new[$name[$i]]['name']	=	$name[$i];
					}
				}
			}
			else {
				$new[$event]			=	$details;
				$new[$event]['name']	=	$name;
			}
		}
		
		$obj	=	$new;
		unset($new);
		
		usort($obj, function($a, $b) {
			if((empty($a['@attributes']['after']) && empty($b['@attributes']['after'])) && (empty($a['@attributes']['before']) && empty($b['@attributes']['before'])))
				return 1;
			
			if(!is_array($b['name']))
				$b['name']	=	[$b['name']];

			if(!empty($a['@attributes']['after']) || !empty($b['@attributes']['after'])) {
				
				if(!empty($a['@attributes']['after']) && is_array($b['name']))
					return	(in_array($a['@attributes']['after'], $b['name']))? 1 : -1;
				elseif(!empty($a['@attributes']['after']) && is_string($b['name']))
					return	($a['@attributes']['after'] == $b['name'])? 1 : -1;

				if(!empty($b['@attributes']['after']) && is_array($a['name']))
					return	(in_array($b['@attributes']['after'], $a['name']))? 1 : -1;
				elseif(!empty($b['@attributes']['after']) && is_string($a['name']))
					return	($b['@attributes']['after'] == $a['name'])? 1 : -1;
			}
			else {
				if(!empty($a['@attributes']['before']))
					return	(in_array($a['@attributes']['before'], $b['name']))? -1 : 1;

				if(!empty($b['@attributes']['before']))
					return	(in_array($b['@attributes']['before'], $a['name']))? -1 : 1;
			}
		});
		
		foreach($obj as $key => $object) {
			
			if(!is_array($object['name']))
				$object['name']	=	[$object['name']];
			
			if(!empty($object['@attributes'])) {
				$REQ	=	$object['@attributes'];
				
				if(!empty($object['@attributes']['request'])) {
					if($REQ['request'] == 'post' && !in_array($this->getPost('action'), $object['name'])) {
						unset($obj[$key]);
					}
					elseif($REQ['request'] == 'get' && !in_array($this->getGet('action'), $object['name']))  {
						unset($obj[$key]);
						continue;
					}
				}
				
				if(!empty($REQ['is_ajax'])) {
					$ajax_on	=	($REQ['is_ajax'] == 'true');
					if((!$this->isAjaxRequest() && $ajax_on) || ($this->isAjaxRequest() && !$ajax_on)) {
						unset($obj[$key]);
						continue;
					}
				}
				
				if(!empty($REQ['is_admin'])) {
					$admin_on	=	($REQ['is_admin'] == 'true');
					if((!$this->isAdmin() && $admin_on) || ($this->isAdmin() && !$admin_on)) {
						unset($obj[$key]);
						continue;
					}
				}
				
				if(!empty($REQ['is_loggedin'])) {
					$is_loggedin_on	=	($REQ['is_loggedin'] == 'true');
					if((!$this->isLoggedIn() && $is_loggedin_on) || ($this->isLoggedIn() && !$is_loggedin_on)) {
						unset($obj[$key]);
						continue;
					}
				}
			}
		}
		
		$this->doWorkflow(['object' => $obj]);
		return $this;
	}
}