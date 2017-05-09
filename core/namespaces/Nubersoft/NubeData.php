<?php
namespace Nubersoft;

// Allows setting loading
class NubeData extends \Nubersoft\nFunctions
	{
		public	static	$settings,
						$errors,
						$incidentals,
						$properties		=	array();
		
		public	function __get($property)
			{
				return (isset(self::$properties[$property]))? self::$properties[$property]:false;
			}
		
		public	function __set($property,$value)
			{
				self::$properties[$property]	=	$value;
			}
		
		public	function __isset($property)
			{
				return	(isset(self::$properties[$property]))? true:false;			
			}
	
		// Used to validate template directory/files
		public	function activeElement($dir = false,$file = false)
			{
				// If input is empty -> false
				if(($dir == false))
					return false;
				// If input is not a directory -> false
				elseif(!is_dir($dir))
					return false;

				$valid_dir	=	scandir($dir);
				// Include file if is in directory
				if(in_array($file,$valid_dir))
					return true;
			}
		
		public	function callEngine()
			{
				return $this;
			}
		/*
		**	@description	Calls data from the data node
		*/
		public	function __call($name,$args=false)
			{
				$name		=	preg_replace('/^get/','',$name);
				$getMethod	=	preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
				$sub		=	'settings';
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
				
				if(isset(self::${$sub}->{$getKey})) {
					if(!empty($search)) {
						$array	=	$this->getMatchedArray($search,'_',$this->toArray(self::${$sub}->{$getKey}));
						if(!empty($array)) {
							$returnVal	=	$array[end($search)];
							$value		=	(is_array($returnVal) && count($returnVal) == 1)? $returnVal[0] : $returnVal;
							return (is_array($value))? $this->toObject($value) : $value;
						}
					}
					else
						return self::${$sub}->{$getKey};
				}
			}
			
		public	function getIncidentals()
			{
				return self::$incidentals;
			}
			
		public	function getErrors()
			{
				return self::$errors;
			}
			
		public	function clearNode($node,$type = 'settings')
			{
				if(isset(self::${$type}->{$node}))
					unset(self::${$type}->{$node});
			}
		
		public	function destroy($type=false,$key = false)
			{
				if(!empty($type)) {
					if(!empty($key)) {
						if(isset(self::${$type}->$key))
							unset(self::${$type}->$key);
						
						return;
					}
					
					self::${$type}	=	false;
					return;
				}
				
				self::$settings		=	
				self::$errors		=	
				self::$incidentals	=	
				self::$properties	=	false;
			}
	}