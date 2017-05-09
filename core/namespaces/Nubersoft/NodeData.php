<?php
namespace Nubersoft;

class NodeData extends \Nubersoft\NubeData
	{
		protected	$nodeDataArr;
		
		public	function getSettingsNode($name = false)
			{
				$data	=	$this->toArray(self::$settings);
				
				if(!empty($name))
					return (isset($data[$name]))? $data[$name] : false;
					
				return $data;
			}
			
		public	function getNode($name)
			{
				$this->nodeDataArr	=	$this->getSettingsNode($name);
				
				return $this;
			}
		/*
		**	@description	Calls data from the data node
		*/
		public	function __call($name,$args=false)
			{
				$name		=	preg_replace('/^get/','',$name);
				$getMethod	=	preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
				$search		=	false;
				if(!empty($args[0])) {
					if(is_array($args[0])) {
						$search	=	$args[0];
						if(!empty($args[1]) && is_string($args[1]))
							$sub	=	$args[1];
					}
					else {
						if(!empty($args[0]) && is_string($args[0]))
							$sub	=	$args[0];
					}
				}
				
				$getKey		=	strtolower(implode('_',$getMethod));
				
				if(isset($this->nodeDataArr[$getKey]))
					$getDataSet	=	$this->nodeDataArr[$getKey];
				elseif(isset($this->nodeDataArr[$name]))
					$getDataSet	=	$this->nodeDataArr[$name];
				
				//echo printpre($getKey);
				//echo printpre($getDataSet);
				
				if(!empty($getDataSet)) {
					if(!empty($search)) {
						$array	=	$this->getMatchedArray($search,'_',$getDataSet);
						
						if(empty($array))
							$array	=	$this->getMatchedArray($search,'_',$getDataSet);
							
						if(!empty($array)) {
							$returnVal	=	$array[end($search)];
							$value		=	(is_array($returnVal) && count($returnVal) == 1)? $returnVal[0] : $returnVal;
							return (is_array($value))? $this->toObject($value) : $value;
						}
					}
					else
						return $getDataSet;
				}
			}
		
		public function getData()
			{
				return $this->toArray($this->nodeDataArr);
			}
	}