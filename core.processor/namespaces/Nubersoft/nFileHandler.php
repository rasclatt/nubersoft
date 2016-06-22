<?php
namespace Nubersoft;

class nFileHandler
	{
		public	static	$allowClone;
		
		protected	$name,
					$allowTypes,
					$allowedMimeTypes,
					$inclusive,
					$allow,
					$success,
					$dir,
					$data,
					$moveAttr;
		
		private	static	$mimes;
		
		public	function __construct($name = 'file',$checkByMime = false)
			{
				$this->allow		=	false;
				$this->inclusive	=	true;
				$this->setInputName($name);
			}
		
		public	function setInputName($name)
			{
				$this->name	=	$name;
				return $this;
			}
			
		public	function organizeFileArray()
			{
				$files	=	array();
				if(is_array($_FILES[$this->name])) {
					foreach($_FILES[$this->name]['name'] as $key => $value) {
						$files[$key]['name']		=	$_FILES[$this->name]['name'][$key];
						$files[$key]['type']		=	$_FILES[$this->name]['type'][$key];
						$files[$key]['tmp_name']	=	$_FILES[$this->name]['tmp_name'][$key];
						$files[$key]['error']		=	$_FILES[$this->name]['error'][$key];
						$files[$key]['size']		=	$_FILES[$this->name]['size'][$key];
					}
				}
				
				return $files;
			}
		
		public	function allowFileTypes($types = array('jpg'),$inclusive = true)
			{
				$this->inclusive	=	$inclusive;
				$this->allowTypes	=	$types;
				return $this;
			}

		public	function allowMimeTypes($types = array('image/jpeg'))
			{
				$this->allowedMimeTypes	=	$types;
				return $this;
			}
		
		private	function validExt($ext)
			{
				$mimes	=	$this->getAllMimes();
				return (!empty($mimes[$ext]));
			}
		
		public	function sameMime($ext,$mime)
			{
				$mimes	=	$this->getAllMimes();
				return ($this->validExt($ext) && $mimes[$ext] == $mime);
			}
		
		public	function mimeFromExt($ext)
			{
				$mimes	=	$this->getAllMimes();
				$ext	=	trim($ext,'.');
				return (isset($mimes[$ext]))? $mimes[$ext] : false;
			}
		
		public	function getAccFileType($filename)
			{
				$mLocale	=	(defined('APP_MIME_LIST') && is_file(APP_MIME_LIST))? APP_MIME_LIST : __DIR__._DS_.basename(str_replace('\\',_DS_,__CLASS__))._DS_.'apachemimes.txt';
				$finfo		=	new \finfo(FILEINFO_MIME, $mLocale);
				$mime		=	explode(';',$finfo->file($filename));
				$mime[0]	=	trim($mime[0]);
				
				return $mime[0];
			}
		
		public	function getAllMimes()
			{
				if(is_array(self::$mimes) && !empty(self::$mimes))
					return self::$mimes;
				
				$arr		=	array();
				$nFunc		=	\nApp::nFunc();
				$mLocale	=	(defined('APP_MIME_LIST') && is_file(APP_MIME_LIST))? APP_MIME_LIST : __DIR__._DS_.basename(str_replace('\\',_DS_,__CLASS__))._DS_.'apachemimes.txt';
				$types		=	file($mLocale);
				foreach($types as $mime) {
					if(preg_match('/^([\#]{1}).*/',$mime))
						continue;
					$mime		=	trim($mime);
					$tabbed		=	explode("\t",$mime);
					$tabbed		=	array_values(array_filter($tabbed));
					$mimeName	=	trim($tabbed[0]);
					$tabbed[1]	=	trim($tabbed[1]);
					
					if(strpos($tabbed[1]," ") !== false) {
						$expAll	=	explode(" ",$tabbed[1]);
						foreach($expAll as $extension) {
							$extension			=	trim($extension);
							$arr[$extension]	=	$mimeName;
						}
					}
					else {
						$extension			=	trim($tabbed[1]);
						$arr[$extension]	=	$mimeName;
					}
				}
				
				ksort($arr);
				
				self::$mimes	=	$arr;
				
				return self::$mimes;
			}
		
		public	function setDestination($url,$settings = array('make'=>true))
			{
				$make		=	(!empty($settings['make']) || (!isset($settings['make'])));
				$protect	=	(!empty($settings['protect']));
				
				if(!is_dir($url) && $make) {
					try{
						if(!@mkdir($url,0755,true))
							throw new \Exception("Error while creating folder: {$url}. Check permissions or proper path");
					}
					catch(\Exception $e) {
						if(is_admin()) {
							die($e->getMessage());
						}
						
						\nApp::saveToLogFile('error_uploads.txt',$e->getMessage());
					}
				}
				
				if($protect) {
					$base	=	str_replace(_DS_._DS_,_DS_,$url._DS_);
					if(!is_file($base.'.htaccess')) {
						\nApp::nFunc()->autoload('CreateHTACCESS',NBR_FUNCTIONS);
						CreateHTACCESS(array('dir'=>$base,'make'=>true));
						$this->success['htaccess']['success']	=	(!is_file($base.'.htaccess'))? 'err' : 'ok';
					}
				}
				
				$this->dir	=	$url;
				
				return $this;
			}
		
		public	function moveFiles()
			{
				$settings	=	(!empty($this->moveAttr))? $this->moveAttr : array('overwrite'=>true);
				
				$this->dir	=	(!empty($this->dir))? $this->dir : NBR_ROOT_DIR;
				
				if(empty($this->allow))
					return false;
				
				foreach($this->allow as $files) {
					
					$useUnique	=	(!empty($settings['unique']))? $files['unique_name'] : $files['file_name'];
					$this->success[$files['file_name']]['unique']	=	(!empty($settings['unique']))? 'y' : 'n';
		
					if(is_file($filename = str_replace(_DS_._DS_,_DS_,$this->dir._DS_.$useUnique))) {
						$this->success[$files['file_name']]['is_file']			=	'y';
						if(!empty($settings['overwrite'])) {
							$this->success[$files['file_name']]['delete_first']	=	'y';
							unlink($filename);
						}
						else {
							$this->success[$files['file_name']]['success']		=	'skipped';
							$this->success[$files['file_name']]['delete_first']	=	'n';
							continue;
						}
					}
					
					$this->success[$files['file_name']]['success']	=	(move_uploaded_file($files['tmp_name'],$filename))? 'ok': 'fail';
					
					unset($files['name'],$files['tmp_name'],$files['error'],$files['type'],$files['size']);
					ksort($files);
					$this->data[]	=	$files;
				}
			}
		
		public	function setMoveAttr($array)
			{
				if(!is_array($array))
					return false;
				
				foreach($array as $key => $value)
					$this->moveAttr[$key]	=	$value;
	
				return $this;
			}
		
		public	function setObserver()
			{
				if(!isset($_FILES[$this->name]))
					return false;
				// Reorganize the files into block data
				$files		=	$this->organizeFileArray();
				// Get all available mime types 
				$allMimes	=	$this->getAllMimes();
				// Count how many extensions go with each mime type
				$mCount		=	array_count_values($allMimes);
				// Loop through each file attached
				foreach($files as $file) {
					// Get the extension
					$ext						=	strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
					// Split name
					$name						=	trim(str_replace($ext,'',$file['name']),'.');
					$name						=	preg_replace('/[^a-zA-Z0-9\_\.\-]/i','',strip_tags($name));
					$file['name']				=	"{$name}.{$ext}";
					// Get the temp name
					$type						=	$file['type'];
					// Add file path (DB-friendly)
					$file['file_path']			=	str_replace(NBR_ROOT_DIR,'',$this->dir);
					// Save the file size (DB-friendly)
					$file['file_size']			=	$file['size'];
					// Save the file size in MB (DB-friendly)
					$minVal						=	number_format(($file['size']/1024000),3);
					$file['disp_size']['MB']	=	$minVal;
					$file['disp_size']['KB']	=	number_format(($file['size']/1024),2);
					$file['disp_size']['RAW']	=	$file['file_size'];
					// Save file name (DB-friendly)
					$file['file_name']			=	$file['name'];
					// Save unique name
					$file['unique_name']		=	md5($file['name'].date('YmdHis')).".{$ext}";
					$file['full_paths']			=	array(
														'base'=>$file['file_path'].$file['file_name'],
														'unique'=>$file['file_path'].$file['unique_name']
													);
					// Save file ext (DB-friendly)
					$file['file_ext']			=	$ext;
					// Save file mime (DB-friendly)
					$file['file_mime']			=	$type;
					// Get the temp name
					$tempName					=	$file['tmp_name'];
					// Find the mime from the list
					$getMime					=	$this->mimeFromExt($ext);
					// Set staging array
					$gMimes						=	array();
					// See if mime is set
					if(isset($mCount[$getMime]) && $mCount[$getMime] > 1) {
						// If there are more than one extensions associate with mime (like jpg,jpeg,etc..)
						// loop through all the mimes and get accepted extensions
						$gMimes	=	array_map(function($v) use ($getMime) {
							if($v == $getMime)
								return $getMime;
						}, $allMimes);
						// Get all accepted extensions used by mime
						$extMime	=	array_keys(array_filter($gMimes));
					}
					else
						// IF there is only one, just assign it.
						$extMime	=	array($ext);
					
					if(!empty($this->allowedMimeTypes)) {
						if(!in_array($type,$this->allowedMimeTypes)) {
							$this->success[$file['name']]['success']	=	'invalid';
							continue;
						}

						$this->allow[]	=	$file;
					}
					elseif(!empty($this->allowTypes)) {
						if(empty($extMime))
							return false;
							
						$validExt	=	array_intersect($this->allowTypes,$extMime);
						
						if(!empty($validExt)) {
							$this->allow[]	=	$file;
						}
						else
							$this->success[$file['name']]['success']	=	'invalid';
					}
				}
				
				return $this;
			}
		
		public	function setCallback($func)
			{
				self::$allowClone['data']	=	false;
				self::$allowClone['set']	=	false;
				
				if(!is_array($this->allow))
					$this->allow	=	array();
				// Name of class or function (see call_user_func_array() in php manual to see how to pass)
				$cName	=	(!empty($func['name']))? $func['name'] : false;
				// Specify class or function, function is default
				$cType	=	(!empty($func['type']))? $func['type'] : 'func';
				
				if($cType == 'func')
					$cName($this->allow,$this->data,$this->allowTypes,$this->dir,$this->name,$this->success);
				elseif($cType == 'class')
					call_user_func_array($cName,$this->allow);
				
				return $this;
			}
		
		public	function upload()
			{
				$useArray	=	(!empty(self::$allowClone['set']))? self::$allowClone['data'] : $this->allow;
				
				if(!empty($useArray)) {
					$this->allow	=	$useArray;
					$this->moveFiles();
				}
			}
			
		public	function getErrors()
			{
				return (!empty($this->success))? $this->success : array();
			}
			
		public	function getUploadData()
			{
				return (!empty($this->data))? $this->data : array();
			}
	}