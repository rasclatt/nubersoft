<?php
namespace Nubersoft;

class nAlert extends NubeData
{
	# Create a static array for storage
	protected	static $alerts	=	[];
	/**
	*	@description	Save an "warning" alert
	*/
	public	function saveAlert($msg, $type = 'general')
	{
		$this->doGlobalSave($msg,$type,'alert');
		return $this;
	}
	/**
	*	@description	Save an "success" alert
	*/
	public	function saveSuccess($msg, $type = 'general')
	{
		$this->doGlobalSave($msg,$type,'success');
		return $this;
	}
	/**
	*	@description	Save an "error" alert
	*/
	public	function saveError($msg, $type = 'general')
	{
		$this->doGlobalSave($msg,$type,'error');
		return $this;
	}
	/**
	*	@description	Saves any kind of alert
	*/
	public function doGlobalSave($msg,$type,$kind)
	{
		# If the value is not already saved
		if(!isset(self::$alerts[$kind][$type])) {
			# Create new data archiver
			$Methodize	=	new Methodize();
			# Save the attribute
			$Methodize->saveAttr($type,array($msg));
			# Store the data
			self::$alerts[$kind][$type]	=	$Methodize;
		}
		else {
			# Push the new message into the current same-named array
			self::$alerts[$kind][$type]	=	self::$alerts[$kind][$type]->useCallback(function($array) use ($msg,$type){
				# If already set
				if(!empty($array[$type])) {
					# If it's not an array (should by default be) if using built-in methods
					if(!is_array($array[$type])) {
						# Create new array and store to array the current value
						$new[$type][]	=	$array[$type];
						# Add the new value
						$new[$type][]	=	$msg;
						# Re-assign the current array to the new one
						$array	=	$new;
						# Return back the current one
						return $array;
					}
					else {
						# Add the value to the current array
						$array[$type][]	=	$msg;
						return $array;
					}
				}
			});
		}
	}
	/**
	*	@description	Returns any sort of array
	*/
	public	function __call($name,$args=false)
	{
		# Strip out the get part of the name
		$key	=	strtolower(str_replace('get','',$name));
		# Store default
		$arg	=	(!empty($args[0]))? $args[0] : 'general';
		# If there is an alert already
		if(isset(self::$alerts[$key]))
			return (isset(self::$alerts[$key][$arg]))? self::$alerts[$key][$arg]->{"get{$arg}"}() : false;
		# Just return empty
		return new Methodize();
	}
	/**
	*	@description	Alias of __call()
	*/
	public	static function __callStatic($name,$args=false)
	{
		$args	=	(!empty($args[0]))? $args[0] : false;
		return nApp::call()->getHelper('nAlert')->{$name}($args);
	}
}