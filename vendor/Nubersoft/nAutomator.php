<?php
namespace Nubersoft;

class nAutomator extends \Nubersoft\nApp
{
	use nMarkUp\enMasse;
	
	public	function getClientWorkflow($type)
	{
		return $this->getWorkflowFile($type, NBR_CLIENT_WORKFLOWS);
	}
	
	public	function getSystemWorkflow($type)
	{
		$workflow	=	$this->getWorkflowFile($type, NBR_WORKFLOWS);
		
		if(empty($workflow))
			throw new HttpException('System Workflow ('.$this->enc($type).') is missing or invalid.'.printpre(false), 100);
		
		return $workflow;
	}
	
	public	function getClientBlockflow($type)
	{
		return $this->getWorkflowFile($type, NBR_CLIENT_BLOCKFLOWS);
	}
	
	public	function getSystemBlockflow($type)
	{
		$workflow	=	$this->getWorkflowFile($type, NBR_BLOCKFLOWS);
		
		if(empty($workflow))
			throw new HttpException('System Blockflow ('.$this->enc($type).') is missing or invalid.'.printpre(false), 100);
		
		return $workflow;
	}
	
	public	function getWorkflowFile($type, $from)
	{
		return $this->getHelper('Conversion\Data')->xmlToArray($from.DS.$type.'.xml');
	}
	
	public	function doClassWorkflow($array)
	{
		$class	=	$array['name'];
		$method	=	$array['method'];
		
		if(!empty($array['inject'][$method])) {
			$args	=	$this->doInjection($array['inject'][$method]);
		}
		
		if(!empty($args)) {
			$Obj	=	new $class();
			$Obj->{$method}(...$args);
		}
		else {
			$Reflect	=	new \Nubersoft\nReflect();
			$Obj		=	$Reflect->reflectClassMethod($array['name'], $array['method']);
		}
		
		if(!empty($array['chain'])) {
			
			if(is_string($array['chain'])) {
			   $array['chain']	=	[
				   $array['chain']
			   ];
			}
			
			foreach($array['chain'] as $chain) {
				$inj	=	((!empty($array['inject'][$chain]))? $this->doInjection($array['inject'][$chain]): null);
				$Obj	=	(is_array($inj))? $Obj->{$chain}(...$inj) : $Obj->{$chain}($inj);
			}
		}
		
		return $Obj;
	}
	
	public	function doInjection($array)
	{
		$storage	=	[];
		
		if(isset($array['object'])) {
			foreach($array['object'] as $event => $object) {
				if(isset($object['class'])) {
					foreach($object['class'] as $class) {
						$storage[]	=	$this->doClassWorkflow($class);
					}
				}
			}
		}
		elseif(isset($array['array'])) {
			if(isset($array['array']['arg'])) {
				if(!isset($array['array']['arg'][0]))
					$array['array']['arg']	=	[$array['array']['arg']];
				
				$storage[]	=	$array['array']['arg'];
			}
		}
		return $storage;
	}
	
	public	function doWorkflow($array)
	{
		foreach($array['object'] as $event => $object) {
			if(isset($object['class'])) {
				foreach($object['class'] as $classObj) {
					$this->doClassWorkflow($classObj);
				}
				
			}
		}
	}
	
	public	function normalizeWorkflowArray($array)
	{
		if(!is_array($array))
			return $array;
		elseif(!isset($array['object']))
			return $array;
		
		if(!isset($array['object'][0])) {
			$array['object']	=	[
				$array['object']
			];
		}
		
		$new	=	[];
		
		foreach($array['object'] as $key => $object) {
			$nameAttr	=	$object['@attributes']['event'];
			unset($object['@attributes']['event']);
			if(empty($object['@attributes']))
				unset($object['@attributes']);
			$new['object'][$nameAttr]	=	$object;
			if(isset($array['object'][$key]['class'])) {
				if(!isset($array['object'][$key]['class'][0])){
					$new['object'][$nameAttr]['class']	=
					$array['object'][$key]['class']	=	[$object['class']];
				}
				
				foreach($array['object'][$key]['class'] as $skey => $class) {
					$new['object'][$nameAttr]['class'][$skey]	=	$class;
					if(isset($array['object'][$key]['class'][$skey]['inject'])) {
						$new['object'][$nameAttr]['class'][$skey]['inject']	=
						$array['object'][$key]['class'][$skey]['inject']	=	$this->setInjectName($class['inject']);
						if(!empty($class['inject']['@attributes']))
							$new['object'][$nameAttr]['class'][$skey]['inject']['@attributes']	=	$class['inject']['@attributes'];
					}
					else
						$new['object'][$nameAttr]['class'][$skey]	=	$class;
				}
			}
			elseif(isset($array['object'][$key]['include'])) {
				
				if(!is_array($array['object'][$key]['include'])) {
					$array['object'][$key]['include']	=	[$array['object'][$key]['include']];
				}
				
				foreach($array['object'][$key]['include'] as $incl) {
					$incl	=	$this->useMarkUp($incl);
					if(is_file($incl))
						$this->render($incl, new nRender());
				}
			}
		}
		
		return $new;
	}
	
	protected	function setInjectName($array)
	{
		if(!is_array($array))
			return $array;
		
		if(!isset($array[0])) {
			$array	=	[
				$array
			];
		}
		$new	=	[];
		foreach($array as $injector) {
			$into	=	$injector['@attributes']['into'];
			unset($injector['@attributes']['into']);
			if(empty($injector['@attributes']))
				unset($injector['@attributes']);
			
			$new[$into]	=	$this->normalizeWorkflowArray($injector);
		}
		
		return $new;
	}
}