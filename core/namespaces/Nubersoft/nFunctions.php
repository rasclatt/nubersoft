<?php
/**
*	Copyright (c) 2017 Nubersoft.com
*	Permission is hereby granted, free of charge *(see acception below in reference to
*	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
*	and associated documentation files (the "Software"), to deal in the Software without
*	restriction, including without limitation the rights to use, copy, modify, merge, publish,
*	or distribute copies of the Software, and to permit persons to whom the Software is
*	furnished to do so, subject to the following conditions:
*	
*	The base CMS software* is not used for commercial sales except with expressed permission.
*	A licensing fee or waiver is required to run software in a commercial setting using
*	the base CMS software.
*	
*	*Base CMS software is defined as running the default software package as found in this
*	repository in the index.php page. This includes use of any of the nAutomator with the
*	default/modified/exended xml versions workflow/blockflows/actions.
*	
*	The above copyright notice and this permission notice shall be included in all
*	copies or substantial portions of the Software.
*
*	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*	SOFTWARE.
*SNIPPETS:**
*	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
*	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
*	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;

class	nFunctions extends \Nubersoft\Singleton
{
	private	static	$nHelpers,
					$filedata;

	protected	static	$objectObj;
	protected	$obj;

	private	$rootDir,
			$keyList,
			$actionArr,
			$listenForKey,
			$organizeBy,
			$data;

	public	function __construct($nHtml = false, $nImage = false)
	{
		# Set the html helper
		$this->setHelper('nHtml',$nHtml);
		# Set the image helper
		$this->setHelper('nImage',$nImage);

		return parent::__construct();
	}
	/**
	*	@description	Fetches data from $data
	*/
	public	function fetchData()
	{
		$args	=	func_get_args();
		$val	=	(!empty($args[0]))? $args[0] : false;
		if($val) {
			if(is_object($this->data))
				return (isset($this->data->{$val}))? $this->data->{$val} : false;
			elseif(is_array($this->data))
				return (isset($this->data[$val]))? $this->data[$val] : false;
		}

		return (!empty($this->data))? $this->data : false;
	}
	/**
	*	@description	Passes data from one class to another since $data is private.
	*/
	public	function passToNext()
	{
		# Get content
		$data	=	func_get_args();
		# If there is only one argument, just assign the one argument
		$this->data	=	(count($data) > 1)? $data : $data[0];
		# Send back obj for chaining
		return $this;
	}
	/**
	*	@description	Gets/sets a non-Nubersoft class from the stored array
	*/
	public	function get3rdPartyHelper($type,$dependency = false)
	{
		if(is_object($type))
			$type	=	get_class($type);

		if($this->helperIsSet($type,"")) {
			return self::$nHelpers[$type];
		}
		else {
			$this->setHelper($type,$dependency,"");
			return (isset(self::$nHelpers[$type]))? self::$nHelpers[$type] : false;
		}
	}
	/**
	*	@description	Wrapper for $this->get3rdPartyHelper()
	*/
	public	function getPlugin($type,$dependency = false)
	{
		return $this->get3rdPartyHelper($type,$dependency);
	}
	/**
	*	@description	Gets/sets a class from the stored array
	*/
	public	function getSingleton()
	{
		$args	=	func_get_args();
		$type	=	$args[0];
		if($this->helperIsSet($type)) {
			return self::$nHelpers[$type];
		}
		else {
			unset($args[0]);
			$args	=	(count($args) >= 1)? $args : false;
			$this->setHelper($type,$args);
			return (isset(self::$nHelpers[$type]))? self::$nHelpers[$type] : false;
		}
	}
	/**
	*	@description	Fetches a Nubersoft-based class
	*/
	public	function getHelper()
	{
		$args	=	func_get_args();
		$type	=	$args[0];
		if($this->helperIsSet($type)) {
			return self::$nHelpers[$type];
		}
		else {
			unset($args[0]);
			$args	=	(count($args) >= 1)? $args : false;
			$this->setHelper($type,$args);
			$obj	=	 (isset(self::$nHelpers[$type]))? self::$nHelpers[$type] : false;
			# Unset the value (stops caching them)
			//unset(self::$nHelpers[$type]);
			# Return the object
			return $obj;
		}
	}
	/**
	*	@description	Checks if a class has been set into the helper class
	*/
	protected	function helperIsSet($type,$namespace = '\Nubersoft\\')
	{
		if(is_object($type))
			$type	=	get_class($type);

		if(!is_string($type) || !is_string($namespace)) {
			trigger_error('Namespace or Class name must be string.');
			die(printpre(array($namespace,$type)));
		}

		$class	=	"{$namespace}{$type}";
		return (isset(self::$nHelpers[$type]) && is_a(self::$nHelpers[$type], $class));
	}
	/**
	*	@description	This will try and set a helper into static memory
	*/
	public	function setHelper($type,$inject = false,$namespace = '\Nubersoft\\')
	{
		if($this->helperIsSet($type,$namespace))
			return;

		if(is_object($type))
			$type	=	get_class($type);

		$class	=	"{$namespace}{$type}";

		try {
			self::$nHelpers[$type]	=	(is_array($inject))? new $class(...$inject) :  new $class($inject);
		}
		catch (\Exception $e) {
			if($this->isAdmin()) {
				die($e->getMessage());
			}
		}

		return $this;
	}
	/**
	*	@description	Store a class instance into a static array
	*/
	public	function saveEngine()
	{
		$args	=	func_get_args();
		$class	=	$args[0];
		$type	=	trim(preg_replace('/^'.preg_quote('\Nubersoft', '/').'/','',$class),'\\');
		unset($args[0]);
		$inject	=	(!empty($args))? $args : false;
		self::$nHelpers[$type]	=	($inject)? new $class(...$inject) : new $class();
		return $this;
	}
	/**
	*	@description	Try to create and return a class instance
	*/
	public	function returnHelper($type,$inject = false,$namespace = '\Nubersoft\\')
	{
		$class	=	"{$namespace}{$type}";

		try {
			return (is_array($inject))? new $class(...$inject) : new $class($inject);
		}
		catch (\Exception $e) {
			if($this->isAdmin()) {
				die($e->getMessage());
			}
		}

		return $this;
	}
	/**
	*	@description	Returns if a array key is set or not
	*/
	private	function validateVar($array = false,$key = false)
	{
		if(is_array($array))
			return (isset($array[$key]));

		return false;
	}
	/**
	*	@description	Checks if an array key/value is empty and can check that it equals a certain value
	*/
	public	function checkEmpty($array = false,$key = false,$value = false)
	{
		if($this->validateVar($array,$key)) {
			if(!empty($array[$key])) {
				# If there is a value component return true or false if it matches value
				if($value != false)
					return ($array[$key] === $value)? $array[$key] : false;
				# If the value is not empty
				return $array[$key];
			}
		}
		# If gets to here, empty
		return false;
	}
	/**
	*	@description	Returns a value of an array or object and can set it's
	*					default value if original is empty
	*/
	protected	function setKeyValue($array,$key,$false,$true = false)
	{
		if(is_object($array))
			return (isset($array->{$key}))? ((empty($true))? $array->{$key} : $true) : $false;

		return (isset($array[$key]))? ((empty($true))? $array[$key] : $true) : $false;
	}

	protected	function setKeyValueBool($array,$key)
	{
		return (isset($array[$key]));
	}
	/**
	*	@description	Scans a directory and returns array similar to the recursive method
	*/
	public	function getNonRecDir($dir = false,$search = false)
	{
		if(is_dir($dir)) {
			$scanned	=	scandir($dir);
			foreach($scanned as $filename) {
				if($filename == '.' || $filename == '..')
					continue;

				$path	=	$this->toSingleDs($dir.DS.$filename);

				if(is_dir($path))
					$files['dirs'][]	=	$path;
				else {
					if(empty($search) || (!empty($search) && preg_match("/".$search."$/",$filename))) {
						$files['host'][]	=	
						$files['root'][]	=	$path;
					}
				}
			}
		}

		return (!empty($files))? $files : false;
	}
	/**
	*	@description	Sets a root dir to variable
	*/
	public	function setRootDir($value = false)
	{
		$this->rootDir	=	$value;
		return $this;
	}
	/**
	*	@description	Fetches all directories/files from a directory recursively into an array
	*/
	public	function getDirList($settings = false,$return = false)
	{
		if(is_string($settings))
			$directory	=	$settings;
		else
			$directory	=	(!empty($settings['dir']))? $settings['dir'] : false;

		$encode			=	$this->checkEmpty($settings,'enc',true);
		$filetype		=	(!empty($settings['type']) && is_array($settings['type']))? $settings['type']:false;
		if(!isset($settings['recursive']))
			$recursive	=	true;
		else
			$recursive	=	$settings['recursive'];
		$addpreg		=	($filetype != false)? "\.".implode("|\.",$filetype) : "\.php|\.csv|\.txt|\.htm|\.css|\.htm|\.js";
		$array			=
		$array['dirs']	=
		$array['host']	=
		$array['root']	=	array();

		if(!is_dir($directory))
			return false;

		if(!$recursive)
			return $this->getNonRecDir($directory,$addpreg);

		$dir	=	new \RecursiveIteratorIterator(
						new \RecursiveDirectoryIterator($directory),
						\RecursiveIteratorIterator::CHILD_FIRST
					);

		# Loop through directories
		while($dir->valid()) {
			# If there is a specific value to return
			$render	=	($filetype == false);
			try {
				$file = $dir->current();

				ob_start();
				echo $file;
				$data	=	ob_get_contents();
				ob_end_clean();

				$data	=	trim($data);
				# Search for files and folders
				if(preg_match('/'.$addpreg.'$/',basename($data),$ext)) {
					# If there is an array to return for file type and a match is found
					if($filetype != false && isset($ext[0]))
						$render	=	(in_array(ltrim($ext[0],"."),$filetype));

					if($render)
						$array['list'][]	=	($encode)? urlencode(Safe::encode(base64_encode($data))):$data;
				}

				if($render) {
					if(basename($data) != '.' && basename($data) != '..') {
						$array['host'][]	=	$data;
						$array['root'][]	=	str_replace($this->rootDir,"",$data);
						if(is_dir($data) && !in_array($data.DS,$array['dirs'])) {
							$array['dirs'][]	=	$data.DS;
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

		if(!empty($return))
			return (!empty($array) && isset($array[$return]))? $array[$return] : false;

		return (isset($array))? $array:false;
	}
	/**
	*	@description	Includes all files inside a destination folder
	*/
	public	function autoloadContents($dir,$type='include_once')
	{
		$files	=	$this->getNonRecDir($dir,'.php');

		if($files) {
			foreach($files['root'] as $file) {
				switch($type) {
					case('include'):
						include($file);
						break;
					case('require'):
						require($file);
						break;
					case('require_once'):
						require_once($file);
						break;
					default:
						include_once($file);
				}
			}
		}

		return $this;
	}
	/**
	*	@description	Basic santize function to create html friendly values from all GLOBAL arrays
	*/
	public	function sanitizeRequests()
	{
		$sanitize	=	new Submits();
		# Loop through and htmlentities sanitize post,get,request
		$sanitize->sanitize();

		return $this;
	}
	/**
	*	@description	Autoloads a function
	*/
	public	function autoload($func, $dir = false, $prefix = '',$ext = 'php')
	{
		# Set where to load files from
		$dir	=	(!empty($dir))? rtrim($dir,DS) : NBR_FUNCTIONS;
		# Load up the functions
		nAutoloadAsset::autoloadFunction($func,$dir,$prefix,$ext);
		return $this;
	}
	/**
	*	@description	Recursively fines values associated with a key
	*/
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
		if(!is_array($array))
			return $this;
		
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
	/**
	*	@description	Returns values created by $this->findByKeyOrder() or by $this->findKey()
	*/
	public	function getKeyList($val = false)
	{
		if(!empty($val))
			return	(!empty($this->keyList[$val]))? $this->keyList[$val] : false;
		else
			return	(!empty($this->keyList))? $this->keyList : array();
	}
	/**
	*	@description	This will search an array recursively and find the named key then organize by nested key name
	*	@param	$array	[array]			This is the array to search through
	*	@param	$key	[string]		This is the key the search is looking for
	*	@param	$organizeBy	[string]	If an array is found, then there is an attempt to order by keyname (like array_column())
	*/
	public	function extractArray($array = false,$key = 'action',$organizeBy = 'name')
	{
		if(!is_array($array))
			return false;
		# Fetch any arrays where the key name is equal to $key
		$use	=	$this->findKey($array,$key)->getKeyList();
		# Save a push array (to extract mulitple arrays inside an array)
		$pushed	=	array();
		# This accounts for mulitple actions in one config file.
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
		# Organize by action name
		$runActions	=	$this->organizeByKey($pushed,$organizeBy);
		# Return the new array
		return (is_array($runActions))? $runActions : array();
	}
	/**
	*	@description				This function checks that a folder exists, if not, will create one with permissions
	*	@param	$dir	[string]	Path to directory
	*	@param	$make	[bool]		This tells this function to build folder if not exists
	*	@param	$chmod	[num]		This is the directory permissions.
	*/
	public	function isDir($dir,$make = true,$chmod = 0755)
	{
		if(strlen($dir) > 4096)
			return false;
		
		if(empty($dir))
			trigger_error('Directory can not be empty',E_USER_WARNING);
		else {
			if(!is_dir($dir) && $make) {
				if(!@mkdir($dir,$chmod,true)) {
					trigger_error('Directory failed to be created',E_USER_WARNING);
					return false;
				}
			}

			return is_dir($dir);
		}
		
		return false;
	}
	/**
	*	@description	This is part 1 of chained process to write to disk
	*	@param $filename [string]	This is the path to the destination file
	*	@param $overwrite [bool]	If true, will first delete the file
	*/
	public	function fileSaveTo($filename,$overwrite = true)
	{
		if(is_file($filename))
			unlink($filename);

		$this->filedata['filename']	=	$filename;

		return $this;
	}
	/**
	*	@description	This is part 2 of chain process
	*	@param	[string|array]	This is the content that will saved into the file from the fileSaveTo() function
	*/
	public	function fileUseContent($str)
	{
		$this->filedata['content']	=	(is_array($str))? json_encode($str) : $str;

		return $this;
	}
	/**
	*	@description	This is part 3 of chain process. It will complete the file save process by writing to disk
	*/
	public	function fileWrite()
	{
		nFileHandler::simpleWrite($this->filedata['content'],$this->filedata['filename']);
		return is_file($this->filedata['filename']);
	}
	/**
	*	@desciption	This function will render the contents of any included file
	*	@param	$file [string]		This is the inclusion file path
	*	@param	$dataArray [multi]	This parameter is optional. The included file uses this data to fill variables
	*								inside the $file if there are any. 
	*								Examples: echo $dataArray; # If string
	*								Examples: echo $dataArray['some_string']; # If array
	*								Examples: echo $dataArray->some_string; # If object
	*/
	public	function renderContents($file, $dataArray = false)
	{
		return nRender::simpleRender($file,$dataArray);
	}
	/**
	*	@description	Checks to see if a .htaccess is present
	*	@param	$dir	[string]	Directory to check protection
	*	@param	$rule	[string]	Tells the createHTACCESS what to do (which rule to use, there are only two)
	*	@param	$script	[string]	Setting $rule to false and filling out the $script will save the script instead of default rule
	*/
	public	function isProtected($dir = false,$rule = 'server_rw',$script = false)
	{
		# This particular function does not create directories
		if(empty($dir))
			return false;
		# Create a file string
		$htaccess =	str_replace(DS.DS,DS,$dir.DS.'.htaccess');
		# See if it already exists
		if(is_file($htaccess))
			return true;
		# Make settings for the access builder
		$arr	=	(!empty($script))? array('dir'=>$dir,'script'=>$script) : array('dir'=>$dir,'rule'=>$rule);
		# Load the function 
		$this->autoload('createHTACCESS',NBR_FUNCTIONS);
		# Try to create htaccess file
		createHTACCESS($arr);
		# Check one more time to see if it created properly
		return (is_file($htaccess));
	}
	/**
	*	@description	Takes a nondeliniated string value and turns it to formatted date
	*	@example		$date = $this->dateFromStr('/files/20170528123354.jpg','Y-m-d H:i:s');
	*					# Returns
	*					2017-05-28 12:33:54
	*/
	public	function dateFromStr($num,$format='M j, Y (g:i A)')
	{
		$time	=	preg_replace('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/','$1-$2-$3 $4:$5:$6',$num);
		return date($format,strtotime($time));
	}
	/**
	*	@description	Basic download of a CSV file
	*/
	public	function saveToCSV($array,$title)
	{
		nFileHandler::getCSVFile($array,$title);
	}
	/**
	*	@description	Sets the timezone at point of use
	*/
	public	function setTimeZone($string = 'America/Los_Angeles')
	{
		date_timezone_set($string);
	}
	/**
	*	@description	Returns an array with keys (or not) without creating an error
	*/
	public	function arrayKeys($array)
	{
		return ArrayWorks::{__FUNCTION__}($array);
	}
	/**
	*	@description	Sets data for the current action
	*/
	public	function useData()
	{
		$arg		=	func_get_args();
		$this->data	=	(isset($arg[0]))? $arg[0] : false;
		return $this;
	}
	/**
	*	@description	Fetches a saved object
	*/
	public	function getCurrentSavedObj()
	{
		return self::$objectObj;
	}
	/**
	*	@description	Sets a saved object (or clears it)
	*/
	public	function setCurrentSavedObj($value = false)
	{
		self::$objectObj	=	$value;
		return $this;
	}
	/**
	*	@description	Uses the buffer to turn output to string
	*/
	public	function render()
	{
		$args		=	func_get_args();
		$inc		=	$args[0];
		$types		=	array('include_once','require','require_once');
		$incType	=	'include';
		$useData	=	false;
		unset($args[0]);
		# Store and get the object
		$this->obj	=	$this->setCurrentSavedObj(((!empty($args[1]) && is_object($args[1]))? $args[1] : false))
			->getCurrentSavedObj();
		# Clear it from storage
		if($this->obj)
			$this->setCurrentSavedObj();	

		if(is_array($inc))
			$inc	=	implode(DS,$inc);

		if(!is_file($inc))
			return false;

		if(!empty($args)) {
			foreach($args as $type) {
				if(in_array($type,$types)) {
					$incType	=	$type;
				}
				else {
					$useData	=	$type;
				}
			}
		}
		# Show were things are located
		if(defined('SITE_ORIGIN_RENDER') && SITE_ORIGIN_RENDER == TRUE)
			echo $this->getHelper('nRender')->showBlockOrigin($inc);
		# Start rendering
		ob_start();
		if(is_file($inc)) {
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
		}
		$data	=	ob_get_contents();
		ob_end_clean();

		# Clear the object after use
		$this->obj	=	false;
		# Send back the render
		return $data;
	}
	/**
	*	@description	Extracts a mirrored array from the config(s) or from another array
	*	@example		
	*					$input = array('best','test','messed');
	*					$match = array(
	*						'guess'=>true,
	*						'best'=>array(
	*							'red'=>'blue',
	*							'test'=>array(
	*								'fun'=>array(true),
	*								'messed'=>'something'
	*							)
	*						)
	*					);
	*					
	*					# Returns
	*					Array (
	*						best => Array (
	*							test => Array (
	*								'messed' => 'something'
	*							)
	*						)
	*					);
	*/
	public	function getMatchedArray($array,$split='_',$extArr = false)
	{
		$nApp			=	nApp::call();
		$extArr			=	(is_array($extArr))? $extArr : $nApp->getConfigs();
		$configFuncs	=	new configFunctions(new nAutomator());
		return $configFuncs	->useArray($extArr)
							->getSettings($array);
	}
	/**
	*	@description	Extracts specific characteristics from XML array attributes
	*/
	public	function fetchScripts($array,&$new)
	{
		foreach($array as $key => $value) {
			if(isset($value[0])) {
				$this->fetchScipts($value,$new);
			}
			else {
				if(!isset($value['name']))
					$value['name']	=	'untitled';
				if(!isset($value['loadid']))
					$value['loadid']	=	'na';
				if(!isset($value['loadpage']))
					$value['loadpage']	=	'na';
				if(!isset($value['page_order']))
					$value['page_order']	=	1;
				if(!isset($value['order_after']))
					$value['order_after']	=	'na';
				if(!isset($value['is_admin']))
					$value['is_admin']	=	false;

				$new[]	=	$value;
			}
		}
	}
	/**
	*	@description	Checks if a table exists in the database
	*/
	public	function tableExists($table)
	{
		$tables	=	$this->toArray(nApp::call()->getTables());
		if(is_array($tables) && in_array($table,$tables))
			return true;

		return false;
	}
	/**
	*	@description	Returns an associate array by column names in a table
	*/
	public	function filterArrayByTable($table,$array)
	{	
		if(empty(self::$filedata[$table])) {
			$MySQL	=	new MySQL();
			self::$filedata[$table]	=	$MySQL->getColumns($table);
		}
		
		$cols	=	self::$filedata[$table];
		ArrayWorks::filterByComparison($cols,$array);

		return $cols;
	}
	/**
	*	@description	Takes a value (string, int, bool) and determines it's BOOL value
	*/
	public	function getBoolVal($val)
	{
		return Conversion\Data::{__FUNCTION__}($val);
	}
	/**
	*	@description	Basic human-readable file size builder
	*/
	public	function getByteSize($val,$settings = false)
	{
		return Conversion\Data::{__FUNCTION__}($val,$settings);
	}
	/**
	*	@description	Removes double directory separators with just one
	*/
	public	function toSingleDs($val)
	{
		return (!is_array($val) && !is_object($val))? str_replace(DS.DS,DS,$val) : $val;
	}
	/**
	*	@description	
	*/
	public	function useKeyFromVal($array,$keyname,$unset = false)
	{
		if(is_array($keyname)) {
			$find		=	$this->getMatchedArray($keyname,'',$array);
			$last		=	end($keyname);
			$keyname	=	(!empty($find[$last][0]))? $find[$last][0] : false;
		}
		else
			$keyname	=	(!empty($array[$keyname]))? $array[$keyname] : false;

		if(empty($keyname))
			return $array;

		if($unset) {
			if(isset($array[$unset]))
				unset($array[$unset]);
		}

		$new[$keyname][]	=	$array;

		return $new;
	}
	/**
	*	@description	Basic comparing
	*/
	public	function compare($arg1 = 0,$arg2 = 0,$comp = '=')
	{

		switch ($comp) {
			case ('=') :
				return ($arg1 == $arg2);
			case ('==') :
				return ($arg1 == $arg2);
			case ('===') :
				return ($arg1 === $arg2);
			case ('>') :
				return ($arg1 > $arg2);
			case ('<') :
				return ($arg1 < $arg2);
			case ('<=') :
				return ($arg1 <= $arg2);
			case ('>=') :
				return ($arg1 >= $arg2);
			case ('!=') :
				return ($arg1 != $arg2);
			case ('!==') :
				return ($arg1 !== $arg2);
		}

		return false;
	}
	/**
	*	@description	Creates a json response and dies for ajax-based requests
	*/
	public	function getJson($file)
	{
		if(!is_file($file))
			return false;

		return json_decode(file_get_contents($file));
	}
	/**
	*	@description	Checks if the request is ajax-based
	*/
	public	function isAjaxRequest($type = 'HTTP_X_REQUESTED_WITH')
	{
		return Ajax::{__FUNCTION__}($type);
	}
	/**
	*	@description	Creates a json response and dies for ajax-based requests
	*/
	public	function ajaxResponse($array)
	{
		return Ajax::{__FUNCTION__}($array);
	}
	/**
	*	@description	General alerting for ajax responses
	*/
	public	function ajaxAlert($message,$merge = false)
	{
		return Ajax::{__FUNCTION__}($message,$merge);
	}
	/**
	*	@description	Creates a javascript-based "redirect"
	*/
	public	function ajaxRouter($link)
	{
		return Ajax::{__FUNCTION__}($link);
	}
	/**
	*	@description	Takes an array such as `$_GET` and creates a query string. The query
	*					string can be filtered using an `array` or `string` value. By default
	*					it removes `admintools`. By adding `true` to the last setting, you can
	*					make the variable only include, or only remove key/value pairs.
	*	@example: 
	*					$_GET['key1'] = 'No thank you';
	*					$_GET['key2'] = 'Yes please';
	*					# Option 1
	*					echo $this->createQueryString('key1',$_GET);
	*					# Option 2
	*					echo $this->createQueryString('key1',$_GET,true);
	*					# Option 1 Gives you
	*					key2=Yes+please
	*					# Option 2 Gives you
	*					key1=No+thank+you
	*/
	public	function createQueryString($notvar = false,$request = [],$keep = false)
	{
		return Conversion\Http::{__FUNCTION__}($notvar,$request,$keep);
	}
	/**
	*	@description	Creates a date-based value
	*/
	public	function fetchUniqueId($salt = false,$shuffle = false)
	{
		$number		=	substr(date("YmdHis").preg_replace("/[^0-9]/","",uniqid($salt)),0,49);

		if($shuffle) {
			$number	=	str_split($number);
			shuffle($number);
			$number	=	implode("",$number);
		}
		# Save a quick unique_id
		return	$number;
	}
	/**
	*	@description	Retrieve the html encoder/decoder
	*/
	public	function safe()
	{
		return $this->getHelper('Safe');
	}
	/**
	*	@description	Initializes the Form creation object
	*/
	public	function getForm()
	{
		return $this->getHelper('nForm');
	}
	/**
	*	@description	Turns a value like "table_name_here" to "Table Name Here"
	*/
	public	function colToTitle($title,$uc = true)
	{
		return $this->columnToTitle($title,$uc);
	}
	/**
	*	@description	Alias of above
	*/
	public	function columnToTitle()
	{
		$args	=	func_get_args();
		$title	=	(!empty($args[0]))? $args[0] : false;
		$uc		=	(isset($args[1]))? $args[1] : true;
		
		return ($title)? Conversion::{__FUNCTION__}($title, $uc) : $title;
	}
	/**
	*	@description	Basic parent / child extraction
	*/
	public	function getTreeStructure($info, $parent = 0)
	{
		foreach ($info as $row) {
			$row['parent_id']	=	(isset($row['parent_id']))? $row['parent_id']:'';
			if ($row['parent_id'] == $parent)
				$struc[$row['unique_id']] = $this->getTreeStructure($info, $row['unique_id']);
		}

		$struc	=	(!empty($struc))? $struc: '';

		return $struc; 
	}
	/**
	* @description This function is a recursive iterator, meaning it will
	*              traverse the current array and all children
	* @param   $curr [string|int]  This is the current id value being ready to place
	* @param   $parent [string|int] This is the current parent id being searched
	* @param   $arr [array] This is the array that is being built for the menu structure
	* @param   $array [array]  This is the array pool of ids and parent ids. We are going to pass by reference
	*                          to update this array as we go to fix chicken-before-the-egg scenarios
	* @param   $rKey [int] This is the current key being iterated on in the main array pool
	*/
	public	function nestRecurseIterator($curr,$parent,$arr,&$array,$rKey)
	{   
		# Loop through our menu array to try and match parents
		foreach($arr as $key => $value) {
			# If there is a match
			if($parent == $key) {
				# Remove the key/value pair from main array
				unset($array[$rKey]);
				# Add the id to our menu array
				$arr[$key][$curr]   =   array();
			}
			# If there is no immediate parent match
			else {
				# If the value is an array, try and now look through it for parent, else just continue
				$arr[$key]  =   (is_array($value))? $this->nestRecurseIterator($curr,$parent,$value,$array,$rKey) : $value;
			}
		}
		# Send back this current array
		return $arr;
	}
	/**
	*  @description    This function takes your pool of ids and loops through them, sorting the arrays by parent and child
	*/
	public	function nestedFromFlat($array, $pKey = 'parent_id', $cKey='id')
	{
		$array	=	array_values($array);

		usort($array,function($a,$b) use ($pKey) {
			return (empty($a[$pKey]))? -1 : 1;
		});

		# This is the final storage array
		$arr    =   array();
		# First count to see how many are available
		$count  =   count($array);
		# Start looping
		for($i=0; $i<$count; $i++) {
			$row    =   $array[$i];
			# If there are no parents, the just assign base menu
			if(empty($row[$pKey])) {
				$arr[$row[$cKey]]    =   array();
				# Remove this key/value pair from main array since it's been used
				unset($array[$i]);
			}
			else {
				# Recurse what we currently have stored for the menu
				$new    =   $this->nestRecurseIterator($row[$cKey],$row[$pKey],$arr,$array,$i);
				# If the recurse function didn't find it's parent
				if(isset($array[$i])) {
					# add it to the back of the array
					$array[]    =   $row;
					# Remove the current array
					unset($array[$i]);
					# Recount how many are left to iterate through
					$count      =   count($array);
				}
				# If the parent was found
				else
					# Assign the $new array
					$arr    =   $new;
			}
		}

		# Return the array
		return $arr;
	}
	/**
	*	@description	Basic ip return from SERVER global
	*/
	public	function getClientIp($key = 'REMOTE_ADDR')
	{
		if(!empty(parent::$settings->_SERVER->{$key}))
			return parent::$settings->_SERVER->{$key};
		elseif(isset($_SERVER[$key]))
			return $_SERVER[$key];
	}
	/**
	*	@description	Simple dollar rendering
	*/
	public	function toDollar($string,$curr = '$',$dec=2,$sep=',',$dectype='.',$front=true)
	{
		return Conversion\Money::{__FUNCTION__}($string,$curr,$dec,$sep,$dectype,$front);
	}
	/**
	*	@description	Toggle error reporting
	*/
	public	function setErrorMode($status = false)
	{
		if($status) {
			parent::$settings	=	$this->toArray(parent::$settings);
			# Save status of error method
			parent::$settings['error_mode']	=	["status"=>E_ALL];
			ini_set("display_errors",1);
			error_reporting(E_ALL);
		}
		else {
			parent::$settings	=	$this->toArray(parent::$settings);
			# Save status of error method
			parent::$settings['error_mode']	=	false;
			ini_set('display_errors','off');
			error_reporting(0);
		}
		parent::$settings	=	$this->toObject(parent::$settings);
		return $this;
	}
	/**
	*	@description	Recursively change the keys in an array to upper case or lowercase
	*					Also allows callable functions for custom treatments
	*/
	public	function recurseArrayKeysChanged($array,$type = false,$sort=true)
	{
		return ArrayWorks::{__FUNCTION__}($array,$type,$sort);
	}
	/**
	*	@description	Creates an array with regex values based on the values of an input array<br>
	*					and a target array with the replacement maps
	*	@example		$array = ['TEST'=>'best','REST'=>'fest']  AND $target = ['key1'=>'~TEST~, ~REST~','key2'=>'~REST~']
	*					This example would give you a final array of ['key1'=>'best, fest','key2'=>'fest']
	*/
	public	function interChangeArrays(array $array, array $target,$split='/~[^~]+~/',$trim='~')
	{
		return ArrayWorks::{__FUNCTION__}($array,$target,$split,$trim);
	}
	/**
	*	@description	Recursive trim
	*/
	public	function trimAll($array,$type=false)
	{
		return ArrayWorks::{__FUNCTION__}($array,$type);
	}
	/**
	*	@description	Basic extraction of all keys from the array recursively
	*/
	public	function getRecursiveKeys($array,&$allKeys)
	{
		return ArrayWorks::{__FUNCTION__}($array,$allKeys);
	}
	/**
	*	@description	Basic extraction of all values from the array recursively
	*/
	public	function getRecursiveValues($array)
	{
		return ArrayWorks::{__FUNCTION__}($array);
	}
	/**
	*	@description	Extracts all values from an array recursively
	*/
	public	function extractAll($array,&$new)
	{
		ArrayWorks::{__FUNCTION__}($array,$new);
	}
	/**
	*	@description	Same essential function as array_walk_recursive()
	*					only it will keep the keys the same
	*/
	public	function arrayWalkRecursive($array, $func)
	{
		return ArrayWorks::{__FUNCTION__}($array,$func);
	}
	/**
	*	@description	This will sort an array by a certain key
	*/
	public	function sortByKey($array,$key,$reverse = false)
	{
		return ArrayWorks::{__FUNCTION__}($array,$key,$reverse);
	}
	/**
	*	@description	Replaces key names to match what the form requires
	*/
	public	function replaceKeys(&$array, $match)
	{
		ArrayWorks::{__FUNCTION__}($array,$match);
	}
	/**
	*	@description	Extracts all vallues from a multi-dimensional array and puts them in one
	*/
	public	function flattenArray($array,&$new,$currKey = false)
	{
		ArrayWorks::{__FUNCTION__}($array,$new,$currKey);
	}
	/**
	*	@description	Recursively searches for a key name in an array and returns the value associated with it.
	*/
	public	function getValuesByKeyName($array,$keyname,$useNameAsKey = true)
	{
		$new	=	[];
		ArrayWorks::{__FUNCTION__}($array,$keyname,$new,$useNameAsKey);
		return $new;
	}
	/**
	*	@description	Extracts all values based on the name of a key
	*/
	public	function flattenArrayByKey($array,&$new,$keyName)
	{
		ArrayWorks::{__FUNCTION__}($array,$new,$keyName);
	}
	/**
	*	@description	Splice value into another array at a certain point 
	*/
	public function insertIntoArray(array $array, $insert = '', $placement = 0)
	{
		return ArrayWorks::{__FUNCTION__}($array, $insert, $placement);
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
	public	function organizeByKey($array,$key,$opts = ['unset'=>true,'multi'=>false])
	{
		return ArrayWorks::{__FUNCTION__}($array,$key,$opts);
	}
	
	public	function jsonFromFile($file)
	{
		return Conversion\Data::arrayFromFile($file);
	}
	
	public	function getSystemFile($filename)
	{
		$client	=	$this->toSingleDs(NBR_CLIENT_SETTINGS.DS.$filename);
		if(is_file($client))
			return $client;
		elseif(is_file($core = $this->toSingleDs(NBR_SETTINGS.DS.$filename)))
			return $core;
	}
}