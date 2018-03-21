<?php
namespace Nubersoft;

use \Nubersoft\nAutomator as nAutomator;

class Sortify extends \Nubersoft\nApp
{
	private	$configs,
			$workflow,
			$actionName;
	
	public	function getDefAction()
	{
			return (!empty($this->actionName))? $this->actionName : 'action';
	}

	public	function gatherWorkFlows($type)
	{
			if(!$type)
			throw new nException('Instructions are required');
		
		$thisObj	=	$this;
		
		$this->configs		=	$this->getPrefFile(preg_replace('/[^0-9a-zA-Z\_\-\/]/','',$type),array('save'=>false),false,function($path,$nApp) use ($type,$thisObj) {
			
			$configs	=	$thisObj->recurseNumToTitle($thisObj->getConfigsFromFiles($type));
			
			if(!isset($configs[$type]['base']))
				$configs[$type]['base']	=	array();
				
			if(!isset($configs[$type]['base']['workflow']))
				$configs[$type]['base']['workflow']	=	array();
			if(!isset($configs[$type]['base'][$thisObj->getDefAction()]))
				$configs[$type]['base'][$thisObj->getDefAction()]	=	array();
			
			if(!isset($configs[$type]['client']['workflow']))
				$configs[$type]['client']['workflow']	=	array();
			if(!isset($configs[$type]['client'][$thisObj->getDefAction()]))
				$configs[$type]['client'][$thisObj->getDefAction()]	=	array();
				
			if(!isset($configs[$type]['template']['workflow']))
				$configs[$type]['template']['workflow']	=	array();
			if(!isset($configs[$type]['template'][$thisObj->getDefAction()]))
				$configs[$type]['template'][$thisObj->getDefAction()]	=	array();
			
			foreach($configs[$type] as $kind => $subKind) {
				if(isset($subKind['workflow']['object'])) {
					$configs[$type][$kind]['workflow']	=	$configs[$type][$kind]['workflow']['object'];
				}
			}
			
			return $configs;
		});
		
		return $this;
	}

	public	function setActionName($key)
	{
		$this->actionName	=	$key;
		
		return $this;
	}
	/**
	*	@description	This will retrieve and organize event array(s)
	*/
	public	function execute($type,$append=false,$settings=false)
	{
			# Store the prefs file or not
		$live		=	false;
		# Create storage array
		$new		=	array();
		# Get the raw config files
		$configs	=	$this->getConfigsFromFiles($type,$append,$settings);
		# Assign to pass to anon funcs
		$thisObj	=	$this;
		# Get the workflow
		$workflows	=	$this->getPrefFile('wf_'.$type,array('save'=>$live),false,function($path,$nApp) use ($configs,$thisObj) {
			$combine	=	(!empty($this->getMatchedArray(array('@attributes','combine'),'',$configs)));
			$workflows	=	$nApp->getMatchedArray(array('workflow'),'',$configs);
			$workflow	=	array();
			if(!empty($workflows['workflow']) && is_array($workflows['workflow']))
				$workflow	=	array_pop($workflows['workflow']);
			
			if(!isset($workflows['workflow']))
				$workflows['workflow']	=	array();
			
			if($combine && (count($workflows['workflow']) > 0)) {
				foreach($workflows['workflow'] as $wf) {
					if(!isset($wf['object']))
						continue;
					
					if(isset($wf['object']['@attributes']))
						$wf['object']	=	array($wf['object']);
					
					$workflow	=	array_merge($workflow,$thisObj->nameEventsFromAttr($wf['object']));
				}
			}
			else {
				if(isset($workflow['object']['@attributes']))
					$workflow['object']	=	$thisObj->nameEventsFromAttr(array($workflow['object']));
				else
					$workflow['object']	=	(isset($workflow['object']))? $thisObj->nameEventsFromAttr($workflow['object']) : array();
			}
			
			if(isset($workflow['comment']))
				unset($workflow['comment']);
			
			return $workflow;
		});
		
		$pName		=	'act_'.str_replace('_action','',$this->getDefAction()).'_'.$type;
		$thisObj	=	$this;
		# Get the merged actions array
		$actions	=	$this->getPrefFile($pName,array('save'=>$live),false,function($path,$nApp) use ($configs, $thisObj) {
			$actions	=	$nApp->getMatchedArray(array($thisObj->getDefAction()),'',$configs);
			$action		=	array();
			if(!empty($actions[$thisObj->getDefAction()])) {
				foreach($actions[$thisObj->getDefAction()] as $actionSet) {
					if(isset($actionSet['object']))
						$actionSet	=	array($actionSet);
						
					$actionArr	=	array();
					$thisObj->flattenByObj($actionSet,$actionArr);
					$action	=	array_merge($action,$thisObj->nameEventsFromAttr($actionArr));
				}
			}
			
			return $action;
		});
		
		# Get the instructions for the files
		$instructions	=	$this->getPrefFile('instr_'.$type,array('save'=>$live),false,function($path,$nApp) use ($configs) {
			$attr	=	$nApp->getMatchedArray(array('@attributes'),'',$configs);
			
			if(empty($attr['@attributes']))
				return false;
			
			foreach($attr['@attributes'] as $obj) {
				if(empty($obj['event']))
					continue;
				$event	=	$obj['event'];
				unset($obj['event']);
				$new[$event]	=	(!isset($new[$event]))? $obj : array_merge($new[$event],$obj);
			}
			
			if(empty($new))
				return array();
				
			foreach($new as $event => $obj) {
				if(empty($obj)) {
					unset($new[$event]);
					continue;
				}
				
			}
			
			return (!empty($new))? $new : array();
		});
		
		$workflows	=	$workflows['object'];
		$actionName	=	$this->getRequest($this->getDefAction());
		
		if(!empty($actionName))
			$actions	=	(!empty($actions[$actionName]))? array($actionName => $actions[$actionName]) : array();
		else
			$actions	=	array();
		
		if(!empty($actions))
			$actions	=	$this->filterByActionInstructions($actions,$instructions,$actionName);
		
		//echo strip_tags(printpre($actions));
		
		if(!empty($instructions))
			$workflows	=	$this->filterByInstructions($workflows,$instructions);
		
		if(!empty($actions))
			$workflows	=	$this->insertOrderActions($actions,$workflows,$instructions);
		
		return $workflows;
	}

	protected	function filterByActionInstructions($actions,$instructions,$actionName)
	{
			if(!empty($instructions[$actionName])) {
			$instr				=	$instructions[$actionName];
			$is_request_type	=	$this->checkRequestValid($instr,$actionName);
			$is_ajax			=	(isset($instr['is_ajax']))? $this->getBoolVal($instr['is_ajax']) : false;
			$is_admin			=	(isset($instr['is_admin']))? $this->getBoolVal($instr['is_admin']) : false;
			$is_loggedin		=	(isset($instr['logged_in']))? $this->getBoolVal($instr['logged_in']) : false;
			
			if(!$is_request_type)
				$actions	=	array();
			
			if($is_admin && !$this->isAdmin())
				$actions	=	array();
			
			if($is_ajax && !$this->isAjaxRequest())
				$actions	=	array();
			
			if($is_loggedin && !$this->isLoggedIn())
				$actions	=	array();
		}
		else {
			if($this->getPost($this->getDefAction()) != $actionName)
				$actions	=	array();
		}
		
		return $actions;
	}

	private	function checkRequestValid($instr,$actionName)
	{
			if(!isset($instr['request']))
			return ($this->getPost($this->getDefAction()) == $actionName);
		
		$type	=	strtolower($instr['request']);
		$method	=	"get".ucfirst(strtolower($type));
		
		return ($this->{$method}($this->getDefAction()) == $actionName);
	
	}

	protected	function filterByInstructions($workflows,$instructions)
	{
			foreach($instructions as $event => $instr) {
			if(isset($workflows[$event])) {
				$is_request_type	=	$this->checkRequestValid($instr,$event);
				$is_ajax			=	(isset($instr['is_ajax']))? $this->getBoolVal($instr['is_ajax']) : false;
				$is_admin			=	(isset($instr['is_admin']))? $this->getBoolVal($instr['is_admin']) : false;
				$logged_in			=	(isset($instr['logged_in']))? $this->getBoolVal($instr['logged_in']) : false;
				if(!$is_request_type)
					unset($workflows[$event]);
				
				if($is_admin && !$this->isAdmin())
					unset($workflows[$event]);
				
				if($is_ajax && !$this->isAjaxRequest())
					unset($workflows[$event]);
					
				if($logged_in && !$this->isLoggedIn())
					unset($workflows[$event]);
			}
		}
		
		return $workflows;
	}

	/**
	*	@description	Inserts the actions into the array
	*/
	protected	function insertOrderActions($actions,$workflows,$instructions)
	{
			$event	=	key($actions);
		$before	=	(!empty($instructions[$event]['before']))? $instructions[$event]['before'] : false;
		$after	=	(!empty($instructions[$event]['after']))? $instructions[$event]['after'] : false;
		$new	=	array();
		
		if(!isset($workflows[$before]) && !isset($workflows[$after])) {
			return array_merge($workflows,$actions);
		}
		
		foreach($workflows as $key => $obj) {
			if($key == $before)
				$new[$event]	=	$actions[$event];
			
			$new[$key]	=	$obj;
			
			if($key == $after)
				$new[$event]	=	$actions[$event];
		}
		
		return $new;
	}
	
	public	function getConfigsFromFiles($type,$append = false,$settings=false)
	{
			$configs	=	$this->getPrefFile('workflow_'.$append.$type,array('save'=>true),false,function($path,$nApp) use ($type,$settings) {
			$dirBase	=	(!empty($settings['base']))? $settings['base'] : NBR_SETTINGS;
			$dirClient	=	(!empty($settings['client']))? $settings['client'] : NBR_CLIENT_SETTINGS;
			$dirTemp	=	(!empty($settings['template']))? $settings['template'] : $nApp->toSingleDs(NBR_ROOT_DIR.DS.$nApp->getDefaultTemplate().DS.'settings');
		
			$spots['base']		=	$dirBase.DS.str_replace('flow/','flows/',$type).'.xml';
			$spots['client']	=	$dirClient.DS.str_replace('flow/','flows/',$type).'.xml'; 
			$spots['template']	=	$dirTemp.DS.str_replace('flow/','flows/',$type).'.xml';
			
			$spots['b_actions']	=	$dirBase.DS.preg_replace('!([^/]{1,})/([^/]{1,})!','actions/$2',$type).'.xml';
			$spots['c_actions']	=	$dirClient.DS.preg_replace('!([^/]{1,})/([^/]{1,})!','actions/$2',$type).'.xml';
			$spots['d_actions']	=	$dirTemp.DS.preg_replace('!([^/]{1,})/([^/]{1,})!','actions/$2',$type).'.xml';
			$nRegister			=	$nApp->getHelper('nRegister');
			
			foreach($spots as $webType => $file) {
				if(is_file($file)) {
					if(strpos($webType,'b_') !== false) {
						$webType	=	'base';
					}
					elseif(strpos($webType,'c_') !== false) {
						$webType	=	'client';
					}
					elseif(strpos($webType,'d_') !== false) {
						$webType	=	'template';
					}
					
					if(!isset($configs[$type][$webType]))
						$configs[$type][$webType]	=	$nRegister->parseXmlFile($file);
					else
						$configs[$type][$webType]	=	array_merge($configs[$type][$webType],$nRegister->parseXmlFile($file));
				}
			}
			
			return (!empty($configs))? $configs : array();
		});
		
		return $configs;
	}

	public	function recurseNumToTitle($array)
	{
			$new	=	array();
		foreach($array as $key => $value) {
			
			if(is_numeric($key)) {
				if(!empty($value['@attributes']['event'])) {
					$new[$value['@attributes']['event']]	=	$value;
				}
			}
			else {
				$new[$key]	=	(is_array($value))? $this->recurseNumToTitle($value) : $value;
			}
		}
		
		return $new;
	}

	public	function checkIsNumeric($array)
	{
			$new	=	array();
		foreach($array as $key => $value) {
			if(is_numeric($key))
				return true;
		}
	}
	
	public	function getFlow()
	{
			return $this->workflow;
	}

	public	function baseToNest(&$array,$kind='action')
	{
			if(isset($array[key($array)][$kind]['@attributes'])) {
			$keName		=	$array[key($array)][$kind]['@attributes']['event'];
			$content	=	$array[key($array)][$kind];
			$array[key($array)][$kind]			=	array();
			if(isset($content['object'])) {
				if(!$this->checkIsNumeric($content['object']))
					$array[key($array)][$kind][$keName]	=	$content['object'];
				else
					$array[key($array)][$kind]	=	$this->recurseNumToTitle($content['object']);
			}
			else
				$array[key($array)][$kind][$keName]	=	(isset($content['object']))? $content['object'] : $content;
		}
		
		return $this;
	}

	public	function setOrdering($array)
	{
			if(empty($array))
			return;
		
		$this->baseToNest($array,$this->getDefAction())->baseToNest($array,'workflow');
		
		if($this->getRequest($this->getDefAction())) {
			//echo strip_tags(printpre($this->recurseNumToTitle($array[key($array)]['workflow'])));
			//echo strip_tags(printpre($this->recurseNumToTitle($array[key($array)]['action'])));
		}
		
		$final	=	array_merge($array[key($array)]['workflow'],$array[key($array)][$this->getDefAction()]);
		$new	=	array();
		foreach($final as $event => $attr) {
			if(!isset($attr['@attributes']))
				continue;
			
			foreach($attr['@attributes'] as $row) {
				if(isset($row['request'])) {
					$include	=	false;
					switch(strtolower($row['request'])) {
						case('post'):
							$include	=	$this->getPost($req);
							break;
						case('request'):
							$include	=	$this->getRequest($req);
							break;
						case('get'):
							$include	=	$this->getGet($req);
							break;
					}
					
					if(empty($include))
						continue;
				}
				
				if(isset($row['is_admin'])) {
					if($row['is_admin'] == 1) {
						if(!$this->isAdmin())
							continue;
					}
				}
				
				if(isset($row['is_ajax'])) {
					if($this->getBoolVal($row['is_ajax'])) {
						if(!$this->isAjaxRequest())
							continue;
					}
				}
				
				if(isset($row['logged_in'])) {
					if($this->getBoolVal($row['logged_in'])) {
						if(!$this->isLoggedIn())
							continue;
					}
				}
				
				$arr['before']	=	(isset($row['before']))? $row['before'] : NULL;
				$arr['after']	=	(isset($row['after']))? $row['after'] : NULL;
				
				$this->setOrderBy(array_filter($arr),$event,$new);
			}
			
			unset($final[$event]['@attributes']);
		}
		
		$this->workflow	=	$final;
		return $new;
	}

	public	function getAllAttr($array,$search)
	{
			return $this->getMatchedArray(array($search,'@attributes'),$array);
	}

	public	function orderArrayGen($array)
	{
			$placement	=
		$new		=	array();
		
		foreach($array as $key => $row) {
			$kName	=	(isset($row['name']))? $row['name'] : $key;
			if(isset($row['method']))
				$kName	.=	'\\'.$row['method'];
				
			$new[$kName]	=	$row;
			
			if(isset($row['after'])) {
				$placement[]	=	array($row['after']=>$kName);
			}
			
			if(isset($row['before'])) {
				$placement[]	=	array($kName=>$row['before']);
			}
			
			if(!isset($row['before']) && !isset($row['after'])) {
				$placement[]	=	array($kName=>false);
			}
		}
		
		$holding	=	array();
		//$order		=	array_keys($new);
		foreach($placement as $row) {
			foreach($row as $before => $after) {
				if(empty($after))
					continue;
				
				elseif(!in_array($before,$holding) && !in_array($after,$holding)) {
					$holding[]	=	$before;
					$holding[]	=	$after;
				}
				elseif(in_array($before,$holding) && !in_array($after,$holding)) {
					$placeKey	=	(array_search($before));
					$befPlace	=	($placeKey < 0)? 0 : $placeKey;
					$holding	=	$this->insertIntoArray($holding,$after,$befPlace);
				}
				elseif(!in_array($before,$holding) && in_array($after,$holding)) {
					$placeKey	=	(array_search($after,$holding));
					$holding	=	$this->insertIntoArray($holding,$before,$placeKey);
				}
				elseif(in_array($before,$holding) && in_array($after,$holding)) {
					$this->saveIncidental('sorting_order',array('msg'=>'Events have been ordered already. Further ordering will likely remove a previous ordered item.'));
					
					//$bKey		=	(array_search($before,$holding));
					//$aKey		=	(array_search($after,$holding));
					//$holding	=	$this->nApp->insertIntoArray($holding,$after,$befPlace);
				}
			}
		}
		
	
		if(!empty($holding)) {
			$final	=	array();
			if(count($holding) != count(array_keys($new))) {
				$holding	=	array_merge(array_diff(array_keys($new),$holding),$holding);
			}
			
			foreach($holding as $key) {
				$final[]	=	$new[$key];
			}
			
			return $final;
		}
		else {
			return array_values($new);
		}
	}
	
	public	function setOrderBy($row,$name,&$new)
	{
			$bCount	=	0;
		$aCount	=	0;
		if(isset($row['before'])) {
			$i	=	(!empty($new['position']['before']))? count($new['position']['before']) : 0;
			
			$new['position']['before'][$name.$i]	=	$row['before'];
		}
		if(isset($row['after'])) {
			$i	=	(!empty($new['position']['after']))? count($new['position']['after']) : 0;
			$new['position']['after'][$name.$i]		=	$row['after'];
		}
	}

	public	function filterBy($type,$actionName = false)
	{
			if(empty($this->configs))
			return array();
		
		$new	=	array();	
		# Loop through all configs
		foreach($this->configs as $kType => $configs) {
			# Extract the base kind using type
			if(strpos($kType,$type) !== false) {
				# Reverse sort
				krsort($configs);
				$actions	=
				$workflows	=	array();
				$combine	=	false;
				# Loop through the configs
				foreach($configs as $sKey => $row) {
					# If the action name is set
					if(!empty($actionName)) {
						# If there is no requesting action going on, remove all the actions
						if(empty($this->getRequest($actionName))) {
							if(!empty($row[$this->getDefAction()])) {
								$configs[$sKey][$this->getDefAction()]	=	array();
							}
						}
					}

					if(!empty($configs[$sKey][$this->getDefAction()])) {
						foreach($configs[$sKey][$this->getDefAction()] as $actionEvent => $doAct)
							$actions[$actionEvent]	=	$doAct;
					}

					if(!empty($configs[$sKey]['workflow'])) {
						if(isset($configs[$sKey]['workflow']['@attributes']['combine']))
							$combine	=	true;
						
						if(!empty($workflows)) {
							if($combine) {
								foreach($configs[$sKey]['workflow'] as $actionEvent => $doAct)
									$workflows[$actionEvent]	=	$doAct;
							}
							else
								$workflows	=	$configs[$sKey]['workflow'];
						}
						else
							$workflows	=	$configs[$sKey]['workflow'];
						
					}
				}
				
				$new[$kType]	=	array(
					'workflow' => $workflows,
					$this->getDefAction() => $this->getActionByName($actions,$this->getRequest($actionName))
				);
			}
		}
		
		return $new;
	}
	
	public	function getActionByName($array,$name)
	{
			if(empty($array))
			return $array;
			
		return (isset($array[$name]))? $array[$name] : array();
	}

	public	function getWorkFlowConfigs()
	{
			return $this->configs;
	}

	public	function nameEventsFromAttr($array)
	{
			$new	=	array();
		foreach($array as $key => $value) {
			if(empty($value['@attributes']['event']))
				continue;
			$eName			=	$value['@attributes']['event'];
			unset($value['@attributes']['event']);
			if(empty($value['@attributes']))
				unset($value['@attributes']);
				
			$new[$eName]	=	$value;
		}
		
		return $new;
	}

	public	function flattenByObj($array,&$workflow)
	{
			if(empty($array))
			return;
		elseif(!is_array($array))
			return;
		
		foreach($array as $key => $value) {
			if(isset($value['object'])) {
				if(isset($value['object'][0]))
					$workflow	=	array_merge($workflow,$value['object']);
				else
					$workflow	=	array_merge($workflow,array($value['object']));
			}
			
			if(is_array($value))
				$this->flattenByObj($value,$workflow);
		}
	}
}