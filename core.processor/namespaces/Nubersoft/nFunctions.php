<?php
namespace Nubersoft;

class	nFunctions
	{
		private	static	$singleton;
		private static	$filedata;
		
		private	$rootDir,
				$keyList,
				$actionArr,
				$listenForKey,
				$organizeBy;
		
		
		public	function __construct()
			{
				if(!empty(self::$singleton))
					return self::$singleton;
					
				return self::$singleton	=	$this;
			}
		
		public	function isAjaxRequest()
			{
				return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']));
			}
		
		private	function validateVar($array = false,$key = false)
			{
				
				if($array != false) {
					if(is_array($array)) {
						if(isset($array[$key]))
							return true;
					}
				}
				
				return false;
			}
		
		public	function checkEmpty($array = false,$key = false,$value = false)
			{
				if(self::validateVar($array,$key)) {
					if(!empty($array[$key])) {
						// If there is a value component return true or false if it matches value
						if($value != false)
							return ($array[$key] === $value);
						// If the value is not empty
						return true;
					}
				}
				// If gets to here, empty
				return false;
			}
		
		public	function getNonRecDir($dir = false,$search)
			{
				if(is_dir($dir)) {
					$scanned	=	scandir($dir);
					foreach($scanned as $filename) {
						if(preg_match("/".$search."$/",$filename)) {
							$files['host'][]	=	str_replace(_DS_._DS_,_DS_,$this->rootDir._DS_.str_replace($this->rootDir,"",$dir._DS_.$filename));
							$files['root'][]	=	str_replace($this->rootDir,"",str_replace(_DS_._DS_,_DS_,$dir._DS_.$filename));
						}
					}
				}
					
				return (!empty($files))? $files : false;
			}
		
		public	function setRootDir($value = false)
			{
				$this->rootDir	=	$value;
				return $this;
			}
		
		public	function getDirList($settings = false)
			{
				$directory		=	(!empty($settings['dir']))? $settings['dir']:false;
				$encode			=	$this->checkEmpty($settings,'enc',true);
				$filetype		=	(!empty($settings['type']) && is_array($settings['type']))? $settings['type']:false;
				$recursive		=	(isset($settings['recursive']) && !$settings['recursive'])? false : true;
				$addpreg		=	($filetype != false)? "\.".implode("|\.",$filetype) : "\.php|\.csv|\.txt|\.htm|\.css|\.htm|\.js";
				$array			=	array();
				$array['dirs']	=	array();
				$array['host']	=	array();
				$array['root']	=	array();
				
				if(!is_dir($directory))
					return false;
				
				if(!$recursive) {
					$array	=	self::getNonRecDir($directory,$addpreg);
					return $array;
				}
				
				$dir	=	new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory),\RecursiveIteratorIterator::CHILD_FIRST);
						
				// Loop through directories
				while($dir->valid()) {
					// If there is a specific value to return
					$render	=	($filetype == false);
					try {
						$file = $dir->current();
						
						ob_start();
						echo $file;
						$data	=	ob_get_contents();
						ob_end_clean();
						
						$data	=	trim($data);
						// Search for files and folders
						if(preg_match('/'.$addpreg.'$/',basename($data),$ext)) {
							// If there is an array to return for file type and a match is found
							if($filetype != false && isset($ext[0]))
								$render	=	(in_array(ltrim($ext[0],"."),$filetype));
							
							if($render)
								$array['list'][]	=	($encode)? urlencode(Safe::encode(base64_encode($data))):$data;
						}
						
						if($render) {
							if(basename($data) != '.' && basename($data) != '..') {
								$array['host'][]	=	$data;
								$array['root'][]	=	str_replace($this->rootDir,"",$data);
								if(is_dir($data) && !in_array($data._DS_,$array['dirs'])) {
									$array['dirs'][]	=	$data._DS_;
								}
							}
						}
						
						unset($data);
						
						$dir->next();
					}
					catch (UnexpectedValueException $e) {
						continue;
					}
				}
				
				return (isset($array))? $array:false;
			}
		
		public	function toArray($var = false)
			{
				if(empty($var))
					return $var;
					
				return (is_object($var) || is_array($var))? json_decode(json_encode($var),true) : $var;
			}
			
		public	function toObject($var = false)
			{
				if(empty($var))
					return $var;
					
				return (is_object($var) || is_array($var))? json_decode(json_encode($var)) : $var;
			}
		
		public	function autoloadContents($dir)
			{
				$files	=	$this->getNonRecDir($dir,'.php');
				
				if($files) {
					foreach($files['root'] as $file) {
						include_once($file);
					}
				}
			}
		
		public	function sanitizeRequests()
			{
				$sanitize	=	new Submits();
				// Loop through and htmlentities sanitize post,get,request
				$sanitize->sanitize();
			}
		
		public	function autoload($func,$dir = false)
			{
				$dir	=	(!empty($dir))? rtrim($dir,_DS_) : __DIR__._DS_.'..'._DS_.'functions';
				
				if(is_array($func)) {
					foreach($func as $function) {
						if(function_exists($function))
							continue;	
						
						if(is_file($fFile = $dir._DS_.'function.'.$function.'.php'))
							include_once($fFile);
					}
				}
				else {
					if(function_exists($func))
						return true;
						
					if(is_file($fFile = $dir._DS_.'function.'.$func.'.php'))
						include_once($fFile);
				}
			}
		
		public	function findKey($array,$key)
			{
				foreach($array as $akey => $aval) {
					if($key === $akey)
						$this->keyList[]	=	$array[$akey];
					
					if(is_array($aval)) {
						$this->findKey($aval,$key);
					}
				}
				
				return $this;
			}
		
		
		public	function findByKeyOrder($array,$keyArray)
			{
				foreach($array as $akey => $aval) {
					$newArr	=	$keyArray;
					if(!empty($keyArray[0]) && $keyArray[0] === $akey) {
						unset($newArr[0]);
						
						if(count($keyArray) == 1)
							$this->keyList[$akey][]	=	$array[$akey];
					}

					if(is_array($aval)) {
						$this->findByKeyOrder($aval,array_values($newArr));
					}
				}
				
				return $this;
			}
			
		public	function getKeyList($val = false)
			{
				if(!empty($val))
					return	(!empty($this->keyList[$val]))? $this->keyList[$val] : false;
				else
					return	(!empty($this->keyList))? $this->keyList : array();
			}
		/*
		**	@description			This function is similar to the native PHP array_column()
		**	@param	$array [array]	This is the array to search through
		**	@param	$key [string]	This is the key to turn the array into associative
		**	@param	$opts [array]	These are settings to modify the returned array.
		**							"unset" - removes the searched key/value pair
		**							"multi" - forces the organized arrays into numbered arrays. Without multi, if there are more than one
		**									  arrays with the same key/value, it may mix up data
		*/
		public	function organizeByKey($array,$key = false,$opts = array('unset'=>true,'multi'=>false))
			{
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
							$new[$newKey][]	=	$value;
						else
							$new[$newKey]	=	$value;
					}
				}
				
				return (!empty($new))? $new : array();
			}
		/*
		**	@description	This will search an array recursively and find the named key then organize by nested key name
		**	@param	$array	[array]			This is the array to search through
		**	@param	$key	[string]		This is the key the search is looking for
		**	@param	$organizeBy	[string]	If an array is found, then there is an attempt to order by keyname (like array_column())
		*/
		public	function extractArray($array = false,$key = 'action',$organizeBy = 'name')
			{
				if(!is_array($array))
					return false;
				// Fetch any arrays where the key name is equal to $key
				$use	=	$this->findKey($array,$key)->getKeyList();
				// Save a push array (to extract mulitple arrays inside an array)
				$pushed	=	array();
				// This accounts for mulitple actions in one config file.
				foreach($use as $ukey => $obj) {
					if(!is_array($obj))
						continue;
						
					if(!isset($obj[0]))
						$pushed[]	=	$use[$ukey];
					else {
						$pushed	=	array_merge($pushed,$obj);
						unset($use[$ukey]);
					}
				}
				// Organize by action name
				$runActions	=	$this->organizeByKey($pushed,$organizeBy);
				// Return the new array
				return (is_array($runActions))? $runActions : array();
			}
		/*
		**	@description				This function checks that a folder exists, if not, will create one with permissions
		**	@param	$dir	[string]	Path to directory
		**	@param	$make	[bool]		This tells this function to build folder if not exists
		**	@param	$chmod	[num]		This is the directory permissions.
		*/
		public	function isDir($dir,$make = true,$chmod = 0750)
			{
				if(!is_dir($dir) && $make) {
					mkdir($dir,$chmod,true);
					chmod($dir,$chmod);
				}
				
				return is_dir($dir);
			}
		/*
		**	@description	This is part 1 of chained process to write to disk
		**	@param $filename [string]	This is the path to the destination file
		**	@param $overwrite [bool]	If true, will first delete the file
		*/
		public	function fileSaveTo($filename,$overwrite = true)
			{
				if(is_file($filename))
					unlink($filename);
				
				$this->filedata['filename']	=	$filename;
				
				return $this;
			}
		/*
		**	@description	This is part 2 of chain process
		**	@param	[string|array]	This is the content that will saved into the file from the fileSaveTo() function
		*/
		public	function fileUseContent($str)
			{
				$this->filedata['content']	=	(is_array($str))? json_encode($str) : $str;
				
				return $this;
			}
		/*
		**	@description	This is part 3 of chain process. It will complete the file save process by writing to disk
		*/
		public	function fileWrite()
			{
				$f	=	fopen($this->filedata['filename'],'a+');
				fwrite($f,$this->filedata['content']);
				fclose($f);
			}
		/*
		**	@desciption	This function will render the contents of any included file
		**	@param	$file [string]		This is the inclusion file path
		**	@param	$dataArray [multi]	This parameter is optional. The included file uses this data to fill variables
		**								inside the $file if there are any. 
		**								Examples: echo $dataArray; // If string
		**								Examples: echo $dataArray['some_string']; // If array
		**								Examples: echo $dataArray->some_string; // If object
		*/
		public	function renderContents($file, $dataArray = false)
			{
				ob_start();
				include($file);
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		/*
		**	@description	Checks to see if a .htaccess is present
		**	@param	$dir	[string]	Directory to check protection
		**	@param	$rule	[string]	Tells the createHTACCESS what to do (which rule to use, there are only two)
		**	@param	$script	[string]	Setting $rule to false and filling out the $script will save the script instead of default rule
		*/
		public	function isProtected($dir = false,$rule = 'server_rw',$script = false)
			{
				// This particular function does not create directories
				if(empty($dir))
					return false;
				// Create a file string
				$htaccess =	str_replace(_DS_._DS_,_DS_,$dir._DS_.'.htaccess');
				// See if it already exists
				if(is_file($htaccess))
					return true;
				// Make settings for the access builder
				$arr	=	(!empty($script))? array('dir'=>$dir,'script'=>$script) : array('dir'=>$dir,'rule'=>$rule);
				// Load the function 
				$this->autoload('createHTACCESS',NBR_FUNCTIONS);
				// Try to create htaccess file
				createHTACCESS($arr);
				// Check one more time to see if it created properly
				return (is_file($htaccess));
			}
		
		public	function dateFromStr($num,$format='M j, Y (g:i A)')
			{
				$time	=	preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/','$1-$2-$3 $4:$5:$6',$num);
				return date($format,strtotime($time));
			}

		public	function saveToCSV($array,$title)
			{
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename='.$title.'.csv');
				header('Cache-Control: no-cache, no-store, must-revalidate');
				header('Pragma: no-cache');
				header('Expires: 0');
				
				$f	=	fopen("php://output", "w");
				foreach($array as $row) {
					fputcsv($f, $row);
				}
				
				fclose($f);
				exit;
			}
		
		public	function setTimeZone($string = 'America/Los_Angeles')
			{
				date_timezone_set($string);
			}
		
		public	function arrayKeys($array)
			{
				if(!is_array($array))
					return array();
				
				return (!empty($array))? array_keys($array) : array();
			}
		
		public	function render($inc,$incType = 'include',$useData = false)
			{
				if(is_file($inc)) {
					ob_start();
					switch($incType) {
						case('include_once'):
							include_once($inc);
							break;
						case('require_once'):
							require_once($inc);
							break;
						case('require'):
							require($inc);
							break;
						default:
							include($inc);
					}
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
			}
		
		public	function getMatchedArray($array,$split='_')
			{
				$configFuncs	=	new configFunctions(new \Nubersoft\nAutomator());
				return $configFuncs	->useArray(\nApp::getConfigs())
									->getSettings($array);
			}

		public function insertIntoArray(array $array, $insert = '', $placement = 0)
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
		
		public	function fetchScripts($array,&$new)
			{
				foreach($array as $key => $value) {
					if(isset($value[0])) {
						$this->fetchScipts($value,$new);
					}
					else {
						if(!isset($value['name']))
							$value['name']			=	'untitled';
						if(!isset($value['loadid']))
							$value['loadid']			=	'na';
						if(!isset($value['loadpage']))
							$value['loadpage']		=	'na';
						if(!isset($value['page_order']))
							$value['page_order']		=	1;
						if(!isset($value['order_after']))
							$value['order_after']	=	'na';
						if(!isset($value['is_admin']))
							$value['is_admin']	=	false;
							
						$new[]	=	$value;
					}
				}
			}
		
		public	function tableExists($table)
			{
				$tables	=	$this->toArray(\nApp::getTables());
				if(is_array($tables) && in_array($table,$tables))
					return true;
				
				return false;
			}
		
		public	function filterArrayByTable($table,$array)
			{
				if(empty(self::$filedata[$table])) {
					$qEngine	=	nQuery();
					$query		=	$qEngine->describe($table)->fetch();
					
					if($query == 0)
						return false;
						
					self::$filedata[$table]	=	array_keys(\nApp::nFunc()->organizeByKey($query,'Field'));
				}
	
				$cols	=	self::$filedata[$table];
				$aCols	=	array_diff($cols,array_diff($cols,array_keys($array)));
				$files	=	array();
				foreach($aCols as $key) {
					if(isset($array[$key]))
						$files[$key]	=	$array[$key];
				}
				
				return $files;
			}
		
		public	function flattenArrayByKey($array,&$new,$keyName)
			{
				foreach($array as $key => $value) {
					if($keyName == $key) {
						if(isset($array[$key][0])) {
							$new	=	array_merge($new,$array[$key]);
						}
						else {
							$new[]	=	$array[$key];
						}
					}
				}
			}
	}