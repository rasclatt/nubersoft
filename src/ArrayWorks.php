<?php
namespace Nubersoft;

class ArrayWorks extends \Nubersoft\nApp
{
	private	static	$singleton;
	
	public	function __construct()
	{
		if(self::$singleton instanceof \Nubersoft\ArrayWorks)
			return self::$singleton;
		
		return	self::$singleton	=	$this;
	}
	/**
	*	@description	Recursively change the keys in an array to upper case or lowercase
	*					Also allows callable functions for custom treatments
	*/
	public	static	function recurseArrayKeysChanged($array,$type = false,$sort=true)
	{
		# Return value if not array
		if(!is_array($array))
			return $array;
		# Sort if required
		if($sort)
			ksort($array);
		# Recurse
		foreach($array as $key => $value) {
			# Allow anonymous function
			if(is_callable($type))
				$useKey	=	$type($key);
			# Standard upper or lower
			else
				$useKey	=	($type)? strtoupper($key) : strtolower($key);
			# Save key, recurse
			$return[$useKey]	=	self::recurseArrayKeysChanged($value,$type);
			# Sort
			if($sort && is_array($return[$useKey]))
				ksort($return[$useKey]);
		}

		return (isset($return))? $return : $array;
	}
	/**
	*	@description	Creates an array with regex values based on the values of an input array<br>
	*					and a target array with the replacement maps
	*	@example		$array = ['TEST'=>'best','REST'=>'fest']  AND $target = ['key1'=>'~TEST~, ~REST~','key2'=>'~REST~']
	*					This example would give you a final array of ['key1'=>'best, fest','key2'=>'fest']
	*/
	public	static	function interChangeArrays(array $array, array $target,$split='/~[^~]+~/',$trim='~')
	{
		foreach($target as $key => $value) {
			$match	=	[];
			$value	=	preg_replace_callback($split,function($v) use ($array,$split,$trim){
				$k	=	(is_callable($trim))? $trim($v) : trim($v[0],$trim);
				return (isset($array[$k]))? $array[$k] : $v[0];
			},$value);
			
			$final[$key]	=	$value;
		}
		
		return $final;
	}
	/**
	*	@description	Replaces key names to match what the form requires
	*/
	public	static	function replaceKeys(&$array, $match)
	{
		foreach($array as $key => $value) {
			if(in_array($key,$match)) {
				$array[array_search($key,$match)]	=	$array[$key];
				unset($array[$key]);
			}
		}
	}
	/**
	*	@description	Recursive trim
	*/
	public	static	function trimAll($array,$type=false)
	{
		if(!is_array($array) && !is_object($array)) {
			if(is_callable($type))
				return $type($array);
			else {
				if(empty($array))
					return $array;
				
				return (!empty($type))? trim($array,$type) : trim($array);
			}
		}
		foreach($array as $key => $value)
			$array[$key]	=	self::trimAll($value,$type);

		return $array;
	}
	/**
	*	@description	Basic extraction of all keys from the array recursively
	*/
	public	static	function getRecursiveKeys($array,&$allKeys)
	{
		foreach($array as $key => $value) {
			$allKeys[]	=	$key;
			if(is_array($value)) {
				self::getRecursiveKeys($value,$allKeys);
			}
		}
	}
	/**
	*	@description	Basic extraction of all values from the array recursively
	*/
	public	static	function getRecursiveValues($array)
	{
		if(!is_array($array))
			return $array;
		elseif(empty($array))
			return $array;

		$new	=	array();
		self::extractAll($array,$new);
		return $new;
	}
	/**
	*	@description	Extracts all values from an array recursively
	*/
	public	static	function extractAll($array,&$new)
	{
		foreach($array as $key => $value) {
			if(is_array($value))
				self::extractAll($value,$new);
			else
				$new[]	=	$value;
		}
	}
	/**
	*	@description	Same essential function as array_walk_recursive()
	*					only it will keep the keys the same
	*/
	public	static	function arrayWalkRecursive($array, $func)
	{
		if(!is_callable($func)) {
			trigger_error('2nd parameter needs to be a callable function.',E_USER_NOTICE);
			return $array;
		}
		
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$new[$key]	=	self::arrayWalkRecursive($value,$func);
			}
			else {
				$new[$key]	=	$func($value);
			}
		}

		return (isset($new))? $new : $array;
	}
	/**
	*	@description	This will sort an array by a certain key
	*/
	public	static	function sortByKey($array,$key,$reverse = false)
	{
		usort($array,function($a,$b) use ($key) {
			if(!isset($a[$key]) || !isset($b[$key]))
				return 0;

			 if ($a[$key] == $b[$key])
				return 0;

			if(is_numeric($a[$key]))
				return ($a[$key] < $b[$key]) ? -1 : 1;
			else
				return strcmp($a[$key], $b[$key]);
		});

		foreach($array as $sKey => $sVal) {
			$new["{$key}_{$sKey}"]	=	$sVal;
		}

		if(isset($new) && is_array($new)) {
			if($reverse) {
				$new	=	array_reverse($new);
			}
		}

		return array_values($new);
	}
	/**
	*	@description	Extracts all vallues from a multi-dimensional array and puts them in one
	*/
	public	static	function flattenArray($array,&$new,$currKey = false)
	{
		if(empty($array))
			return false;
		elseif(!is_array($array))
			return false;

		foreach($array as $key => $value) {
			if(!is_numeric($key)) {
				if(!isset($new[$key]))
					$new[$key]	=	array();

				if(is_array($value)) {
					self::flattenArray($value,$new,$key);
				}
				else {
					$new[$key][]	=	$value;
				}
			}
			else {
				if(isset($value[0][0]))
					self::flattenArray($value[$key],$new,$currKey);
				else {
					$new	=	(is_array($new) && is_array($value))? array_merge($new,$value) : $new;
				}
			}
		}
	}
	/**
	*	@description	Recursively searches for a key name in an array and returns the value associated with it.
	*/
	public	static	function getValuesByKeyName($array,$keyname,&$new,$useNameAsKey = true)
	{
		if(empty($array))
			return false;
		
		foreach($array as $key => $value) {
			if($key === $keyname) {
				if($useNameAsKey)
					$new[$keyname][]	=	$value;
				else
					$new[]	=	$value;
			}
			
			if(is_array($value))
				self::getValuesByKeyName($value,$keyname,$new,$useNameAsKey);
		}
	}
	/**
	*	@description	Extracts all values based on the name of a key
	*/
	public	static	function flattenArrayByKey($array,&$new,$keyName)
	{
		foreach($array as $key => $value) {
			if($keyName === $key) {
				if(isset($array[$key][0])) {
					if(is_array($array[$key]))
						$new	=	array_merge($new,$array[$key]);
					else
						$new[]	=	$array[$key];
				}
				else {
					$new[]	=	$array[$key];
				}
			}
			else {
				if(is_array($value)) {
					self::flattenArrayByKey($value,$new,$keyName);
				}
			}
		}
	}
	/**
	*	@description	Splice value into another array at a certain point 
	*/
	public static	function insertIntoArray(array $array, $insert = '', $placement = 0)
	{
		$calc		=	($placement-1);
		$placement	=	($calc < 0)? 0 : $calc;
		$end		=	array_slice($array,$placement);
		$front		=	array_diff($array,$end);
		$front[]	=	$insert;

		if(is_array($front) && is_array($end))
			return array_merge($front, $end);
		elseif(is_array($front) && !is_array($end))
			return $front;
		elseif(!is_array($front) && is_array($end))
			return $end;
	}
	/**
	*	@description			This function is similar to the native PHP array_column()
	*	@param	$array [array]	This is the array to search through
	*	@param	$key [string]	This is the key to turn the array into associative
	*	@param	$opts [array]	These are settings to modify the returned array.
	*							"unset" - removes the searched key/value pair
	*							"multi" - forces the organized arrays into numbered arrays. Without multi, if there are more than one
	*									  arrays with the same key/value, it may mix up data
	*/
	public	static	function organizeByKey($array, $key = false, $opts = false, $func = false)
	{
		$opts	=	(empty($opts))? ['unset' => true, 'multi' => false] : $opts;
		$unset	=	(!isset($opts['unset']) || !empty($opts['unset']));
		$multi	=	(!empty($opts['multi']));

		if(!is_array($array) || empty($key))
			return array();

		foreach($array as $value) {
			if(isset($value[$key])) {
				$newKey	=	$value[$key];

				if($unset)
					unset($value[$key]);

				if($multi)
					$new[$newKey][]	=	(is_callable($func))? $func($value) : $value;
				else
					$new[$newKey]	=	(is_callable($func))? $func($value) : $value;
			}
		}

		return (!empty($new))? $new : array();
	}
	
	public	static	function filterByComparison($arrayKeys, &$array)
	{	
		$aCols			=	array_diff($arrayKeys, array_diff($arrayKeys, array_keys($array)));
		$files			=	[];
		foreach($aCols as $key) {
			if(isset($array[$key]))
				$files[$key]	=	$array[$key];
		}
		
		$array	=	$files;
	}
	/**
	*	@description	Returns an array with keys (or not) without creating an error
	*/
	public	static	function arrayKeys($array)
	{
		if(!is_array($array))
			return array();

		return (!empty($array))? array_keys($array) : array();
	}
	/**
	 *	@description	Takes a string and tries to turn it to an array.
	 *					This includes a json string and a string formated like:
	 *						"key1"="value1","key2"="value2"
	 *					also includes
	 *						"key1"=>"value1","key2"=>"value2"
	 */
	public	static	function convertString($string)
	{
		return Conversion\Data::arrayFromString($string);
	}
	/**
	 *	@description	Recursive function that allows a callable function to apply to the key and value
	 */
	public	static	function recurseApply($array, $func)
	{
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$func($key, $value, self::recurseApply($value, $func));
			}
			else {
				$func($key, $value);
			}
		}
	}
	/**
	 *	@description	
	 */
	public	static	function setKeyValue($array, $key, $value = false)
	{
		if(is_object($array))
			$array	=	(array) $array;
		
		return (isset($array[$key]))? $array[$key] : $value;
	}
	/**
	*	@description	Creates an xml string, able to create an xml document from if required
	*/
	public	static	function toXml($array, $baseArray = 'config', $display = false, $rep = '<?xml version="1.0" encoding="UTF-8"?>')
	{
		$xml	=	new \SimpleXMLElement('<'.$baseArray.'/>');
		$xml	=	self::arrayToXml($array, $xml);
		$xml	=	str_replace('><', '>'.PHP_EOL.'<', $xml->asXML());
		$xml	=	(!empty($rep))? str_replace('<?xml version="1.0"?>', $rep, $xml) : $xml;
		return ($display)? \Nubersoft\nApp::call()->enc($xml) : $xml; 
	}
	/**
	*	@description	Companion method to toXml(). Recursively iterates the array and adds to the xml obj
	*/
	protected	static	function arrayToXml($array, $xml)
	{
		foreach($array as $key => $value) {
			if(is_numeric($key) || is_bool($key))
				$key	=	"var";

			$key	=	str_replace(array(' ', '-', '.'),array('_', '_', ''),strtolower($key));

			if($key === '@attributes') {
				foreach($value as $attrKey => $attrVal) {
					$xml->addAttribute($attrKey, $attrVal);
				}
			}
			else {
				if(is_array($value))
					self::arrayToXml($value, $xml->addChild($key, ''));
				else
					$xml->addChild($key, $value);
			}
		}

		return $xml;
	}
	/**
	 *	@description	Allows the static methods to be called non-static
	 */
	public	function __call($method, $args = false)
	{
		return (count($args) > 0)? self::{$method}(...$args) : self::{$method}();
	}
}