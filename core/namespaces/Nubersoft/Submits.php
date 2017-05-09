<?php
namespace Nubersoft;

class Submits extends \Nubersoft\nApp
	{
		protected	$filter;
		private		static	$charset;
		private		static	$enc = 'UTF-8';
		protected	static	$replacers;
		
		public	function __construct()
			{
				if(empty(self::$charset)) {
					self::$charset	=	$this->setCharSet();
				}
				
				return parent::__construct();
			}
		
		public	function getEncodingType()
			{
				return self::$enc;
			}
			
		public	function setEncodingType($type)
			{
				self::$enc	=	$type;
				return $this;
			}
		
		public	function getCharSet()
			{
				return self::$charset;
			}
		
		private	function setCharSet()
			{
				// Fetches the < > & " characters
				$html				=	get_html_translation_table();
				// Gets all the utf-8 characters
				$hentities			=	get_html_translation_table(HTML_ENTITIES,ENT_NOQUOTES,self::$enc);
				// Gets all the utf-8 characters
				$hspecial			=	get_html_translation_table(HTML_SPECIALCHARS,ENT_NOQUOTES,self::$enc);
				// Combine them all for a super replacer
				$allent				=	array_merge($hspecial,$hentities);
				// Remove the HTML characters from the mix
				return array_diff($allent,$html);
			}
		
		public	function makeRepArrays()
			{
				if(!empty(self::$replacers))
					return self::$replacers;
				
				self::$replacers	= array(
					# Isolate the searchable
					'rawchars'=>array_keys(self::$charset),
					# Isolate the replaceable
					'repchars'=>array_values(self::$charset)
				);
			}
		
		public	function strReplaceChars($value)
			{
				$replacers	=	$this->makeRepArrays();
				# Replace the special characters
				return str_replace($replacers['rawchars'],$replacers['repchars'],$value);
			}
			
		public	function strReplaceCharsRev($value)
			{
				$replacers	=	$this->makeRepArrays();
				# Replace the special characters
				return str_replace($replacers['repchars'],$replacers['rawchars'],$value);
			}
		
		public	function reverseEncoding($array)
			{
				if(!is_array($array))
					return $this->strReplaceCharsRev(html_entity_decode($array, ENT_QUOTES, self::$enc));
					
				foreach($array as $key => $value) {
					$new[$key]	=	$this->reverseArrayEncode($value);
				}
				
				return $new;
			}
		
		protected	function recurseArray($value,$key='')
			{
				$value	=	$this->strReplaceChars($value);
				# Now double encode the html and special chars
				if(!is_array($value)) {
					switch ($this->filter) {
						case ('strip') :
							$val	= 	(is_numeric($value))? (string) $value: strip_tags($value);
							break;
						case ('specialchars') :
							$val	= 	(is_numeric($value))? (string) $value: htmlspecialchars($value,ENT_QUOTES,self::$enc,true);
							break;
						default:
							$val	= 	(is_numeric($value))? (string) $value: htmlentities($value, ENT_QUOTES, self::$enc);
					}
			
					return $val;		
				}
						
				if(is_array($value) && !empty($value)) {
					foreach($value as $keys => $values) {
						$val[$keys]	=	$this->recurseArray($values,$keys);
					}
					
					return $val;
				}
					
			}
			
		public	function sanitize($filter = false)
			{
				$this->filter	=	$filter;

				$array	=	(!empty($_REQUEST))? $this->recurseArray($_REQUEST) : false;
				$this->saveSetting("_REQUEST",$array);
				$this->saveSetting("_SERVER_REQUEST",array('_REQUEST'=>$array));
				
				$array	=	(!empty($_POST))? $this->recurseArray($_POST) : false;
				$this->saveSetting("_POST",$array);
				$this->saveSetting("_SERVER_REQUEST",array('_POST'=>$array));
				
				$this->saveSetting("_RAW_POST",$_POST);
				$this->saveSetting("_SERVER_REQUEST",array('_RAW_POST'=>$_POST));
				
				$array	=	(!empty($_GET))? $this->recurseArray($_GET) : false;
				$this->saveSetting("_GET",$array);
				$this->saveSetting("_SERVER_REQUEST",array('_GET'=>$array));
				
				$this->saveSetting("_RAW_GET",$_GET);
				$this->saveSetting("_SERVER_REQUEST",array('_RAW_GET'=>$_GET));
				
				# Saves $_FILES array to datanode
				$this->setFiles();
				return $this;
			}
			
		public	function sanitizeServer()
			{
				if(!empty($_SERVER)) {
					$array		=	$this->recurseArray($_SERVER);
					$this->saveSetting("_SERVER",$array);
				}
				
				return $this;
			}
			
		public	function setSessionGlobal()
			{
				if(!empty($_SESSION)) {
					$this->saveSetting("_SESSION",$_SESSION,true);
				}
				
				return $this;
			}
		
		public	function unsetSuperGlobals($array)
			{
				if(is_string($array))
					$array	=	array($array);
				
				foreach($array as $type) {
					$type	=	strtolower(str_replace(array('_','$'),'',$type));
					switch($type) {
						case('post'):
							$_POST		=	NULL;
							break;
						case('get'):
							$_GET		=	NULL;
							break;
						case('request'):
							$_REQUEST	=	NULL;
							break;
					}
				}
			}
		
		public	function setFiles()
			{
				$FILES	=	array();
				if(!empty($_FILES)) {
					foreach($_FILES as $key => $row) {
						if(!isset($row['name'][0]))
							continue;
						elseif(!is_array($row['error']))
							continue;
						
						foreach($row['error'] as $i => $val) {
							if($val > 0)
								continue;
							$name	=	preg_replace('/[^0-9a-zA-Z\.\-\_]/','',$_FILES[$key]['name'][$i]);
							
							if(empty(trim($name)))
								continue;
							$size							=	$_FILES[$key]['size'][$i];
							$FILES[$key][$i]['name']		=	$name;
							$FILES[$key][$i]['tmp_name']	=	$_FILES[$key]['tmp_name'][$i];
							$FILES[$key][$i]['error']		=	$_FILES[$key]['error'][$i];
							$FILES[$key][$i]['type']		=	$_FILES[$key]['type'][$i];
							$FILES[$key][$i]['size']		=	$size;
							$FILES[$key][$i]['ext']			=	pathinfo($name,PATHINFO_EXTENSION);
							$kb	=	$this->getByteSize($size,array('from'=>'b','to'=>'kb','round'=>3));
							$mb	=	$this->getByteSize($size,array('from'=>'b','to'=>'mb','round'=>3));
							$gb	=	($mb > 1)? $this->getByteSize($size,array('from'=>'b','to'=>'gb','round'=>3)) : '0.00';
							$FILES[$key][$i]['info']	=	array('kb'=>$kb,'mb'=>$mb,'gb'=>$gb);
						}
					}
				}
				
				$this->saveSetting("_FILES",$FILES);
				$this->saveSetting("_FILES_RAW",$_FILES);
				$this->saveSetting("_SERVER_REQUEST",array('_FILES'=>$FILES));
				$this->saveSetting("_SERVER_REQUEST",array('_FILES_RAW'=>$_FILES));
			}
		
		public	function sanitizeData($array)
			{
				return $this->recurseArray($array);
			}
	}