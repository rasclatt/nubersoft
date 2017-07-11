<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;

class Methodize	extends \Nubersoft\nFunctions
	{
		protected	$info		=	false;
		protected	$is_strict	=	false;
		
		public	function saveAttr($name,$value)
			{
				$this->info[$name]	=	$value;
				return $this;
			}
		
		public	function deleteAttr($name,$value)
			{
				if(isset($this->info[$name]))
					unset($this->info[$name]);
					
				return (isset($this->info[$name]));
			}
		/*
		**	@description	Dynamically call data nodes from stored array(s)
		*/
		public function __call($name,$args=false)
            {
				if(empty($args[0]))
					$args[0]	=	true;
                # Strip off the "get" from the method
                $name       =   preg_replace('/^get/','',$name);
                # Split method name by upper case
                $getMethod  =   preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
                # Create a variable from that split
                $getKey     =   strtolower(implode('_',$getMethod));
				# Create a new instance
				$Methodize	=	new Methodize();
				# Checks if there is a key with this split name
                if(isset($this->info[$getKey])) {
                    $getDataSet =   $this->info[$getKey];
					if(is_bool($args[0]) && $args[0] === true) {
						if(is_array($getDataSet)) {
							foreach($getDataSet as $key => $value) {
								$Methodize->saveAttr($key,$value);
							}
							# Return obj
							return $Methodize;
						}
						else
							return (!empty($getDataSet))? $getDataSet : $Methodize->saveAttr($name,false);
					}
					else {
						if(is_string($args[0]))
							return (isset($getDataSet[$args[0]]))? $getDataSet[$args[0]] : $Methodize->saveAttr($name,false);
					}
					
					return (empty($getDataSet))? $Methodize->saveAttr($name,false) : $getDataSet;
				}
                # Checks if there is a key with the raw name (no get though)
                elseif(isset($this->info[$name])) {
                    $getDataSet =   $this->info[$name];
					if(is_bool($args[0]) && $args[0] === true) {
						if(is_array($getDataSet)) {
							foreach($getDataSet as $key => $value) {
								$Methodize->saveAttr($key,$value);
							}
							
							return $Methodize;
						}
						else
							return $getDataSet;
					}
					else {
						if(is_string($args[0]))
							return (isset($getDataSet[$args[0]]))? $getDataSet[$args[0]] : $Methodize->saveAttr($name,false);
					}
				}
				
                # Returns false
                return $Methodize->saveAttr($name,false);
			}
		/*
		**	@description	Disables the json_encode() of the array when echoing the object
		*/
		public	function setStrictMode($type = true)
			{
				$this->is_strict	=	$type;
				return $this;
			}
		/*
		**	@description	Sets current object to array
		*/
		public	function toArray()
			{
				return $this->safe()->toArray($this->info);
			}
		/*
		**	@description	Sets current object to standard data object
		*/
		public	function toObject()
			{
				return $this->safe()->toObject($this->info);
			}
		/*
		**	@description	Returns a json encoded string unless strict has been toggled
		*/
		public	function __toString()
			{
				if($this->is_strict) {
					trigger_error('String return is set to strict. Use setStrictMode(false) to allow json.',E_USER_NOTICE);
					return '';
				}
				# Check if there is one value in the array
				if(count($this->info) == 1) {
					# Send back just that value
					$vals	=	array_values($this->info);
					return $vals[0];
				}
				# If array, send that back in formatted string
				return json_encode($this->info);
			}
		/*
		**	@description	Serializes the array
		*/
		public	function toSerialize()
			{
				return serialize($this->info);
			}
		/*
		**	@description	Creates an xml string, able to create an xml document from if required
		*/
		public	function toXml($baseArray = 'config',$display = false,$rep = '<?xml version="1.0" encoding="UTF-8"?>')
			{
				$xml	=	new \SimpleXMLElement('<'.$baseArray.'/>');
				$xml	=	$this->arrayToXml($this->info,$xml);
				$xml	=	str_replace('><','>'.PHP_EOL.'<',$xml->asXML());
				$xml	=	(!empty($rep))? str_replace('<?xml version="1.0"?>',$rep,$xml) : $xml;
				return ($display)? $this->safe()->encodeSingle($xml) : $xml; 
			}
		/*
		**	@description	Companion method to toXml(). Recursively iterates the array and adds to the xml obj
		*/
		public	function &arrayToXml($array,$xml)
			{
				foreach($array as $key => $value) {
					if(is_numeric($key) || is_bool($key))
						$key	=	"untitled_{$key}";
					
					$key	=	str_replace(array(' ','-','.'),array('_','_',''),strtolower($key));
					
					if($key === '@attributes') {
						foreach($value as $attrKey => $attrVal) {
							$xml->addAttribute($attrKey,$attrVal);
						}
					}
					else {
						if(is_array($value))
							$this->arrayToXml($value,$xml->addChild($key,''));
						else
							$xml->addChild($key,$value);
					}
				}
				
				return $xml;
			}
		/*
		**	@description	Returns a callable anonymous function. Handy to filter the results or process
		**					the value before it is returned
		*/
		public	function useCallback($func,$methodize = true)
			{
				if(!is_callable($func))
					return false;
				
				if($methodize) {
					$Methodizer	=	new Methodize();
					$Methodizer->saveAttr('methodize',$func($this->info));
					return $Methodizer->getMethodize();
				}
				else
					return $func($this->info);
			}
		/*
		**	@description	Self-explanitory
		*/
		public	function toIterator()
			{
				$Obj	=	new \ArrayObject($this->info);
				return new \RecursiveIteratorIterator(
					new \RecursiveArrayIterator($Obj)
				);
			}
		/*
		**	@description	Forcing a value is good incase a method chain is not available
		**					(mainly for when a series of values don't exist)
		**					Instance of this would be a session variable that may or may not be there.
		**					You can call a series of values without throwing an error
		*/
		public	function toValue()
			{
				return $this->__toString();
			}
		/*
		**	@description	Checks if there is a value to be had
		*/
		public	function hasValue()
			{
				return (!empty($this->__toString()));
			}
	}