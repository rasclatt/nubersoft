<?php
namespace nPlugins\Nubersoft;

class CoreDownloader extends \nPlugins\Nubersoft\CoreDatabase
	{
		protected	$filename;
		protected	$loginReq	=	true;
		
		public	function startDownload()
			{
				$nDownloader	=	$this->getHelper('nDownloader');
				$REQUST	=	$this->toArray($this->getRequest());
				$parsed	=	explode('/',$nDownloader->decode($REQUST['file']));
				$ID		=	(!empty($parsed[0]))? $parsed[0] : false;
				$table	=	(!empty($parsed[1]))? $parsed[1] : false;
				$file	=	$this->select("*, CONCAT(`file_path`,`file_name`) as filename")
								->from($table)
								->where(array('ID'=>$ID))
								->fetch(true);
				
				$this->filename	=	$this->toSingleDs(str_replace('/',DS,NBR_ROOT_DIR.DS.$file['filename']));
				
				if(!is_file($this->filename)) {
					$this->saveIncidental('nbr_download_file',array('msg'=>'File does not exist'));
					return;
				}
			}
		
		public	function getDownloadPath()
			{
				return (!empty($this->filename))? $this->filename : false;
			}
		
		public	function download($usefile = false)
			{
				$usefile	=	(!empty($usefile) && is_string($usefile))? $usefile : $this->getDownloadPath();
				# If allow is already set, assign else false
				$this->allow	=	(!empty($this->allow))? $this->allow : false;
				# If the file is not empty, try to override the class-assigned value(s)
				if($usefile != false) {
					# If the file is a real file
					if(is_file($usefile)) {
						# Assign filename
						$this->filename	=	$usefile;
						# Allow to true
						$this->allow	=	true;
					}
				}
				# If allowed to download
				if($this->allow) {
					$settings["Content-type"]				=	"application/octet-stream";
					$settings["Cache-Control"]				=	array("must-revalidate", "post-check=0", "pre-check=0");
					$settings["Content-Transfer-Encoding"]	=	"binary";
					$settings["Connection"]					=	"Keep-Alive";
					$settings["Expires"]					=	'0';
					$settings["Pragma"]						=	"public";
					$settings["Content-length"]				=	filesize($this->filename);
					$settings["Content-disposition"]		=	'attachment; filename="'.basename($this->filename).'"';
					# Attempt to download the file
					$this->getFile($this->filename,$settings);
					exit;
				}
			}
		
		public	function getFile($file = false, $settings = false)
			{
				# If the file is not real just stop
				if(!is_file($file))
					return false;
				# If there are header settings to override standard
				if(is_array($settings) && !empty($settings)) {
					# Loop through those
					foreach($settings as $name => $vals) {
						# If left blank, just skip to next key/val pair
						if($vals === "")
							continue;
						# If the name is a numeric (non-associative)
						# just output the value
						if(is_numeric($name))
							header($vals);
						else {
							# If name is associative
							# Use the $name as title, implode values
							$useval	=	(is_array($vals))? "{$name}: ".implode(", ",$vals) : "{$name}: {$vals}";
							header($useval);
						}
					}
				}
				# Just use standard downloading
				else {
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-type: application/octet-stream'); 
					header('Content-Transfer-Encoding: binary'); 
					header('Connection: Keep-Alive');
					header('Expires: 0');
					header('Pragma: public');
					header('Content-length: '.filesize($file)); 
					header('Content-disposition: attachment; filename="'.basename($file).'"'); 
				}
					
				readfile($file);
				return true;
			}
		
		public	function setLoginRequired($required = false)
			{
				$this->loginReq	=	(is_bool($required))? $required : $this->getBoolVal($required);
				return $this;
			}
		
		public	function directDownload()
			{
				if($this->loginReq && !$this->isLoggedin())
					return;
					
				$filter	=	array(
					'txt',
					'jpg',
					'jpeg',
					'png',
					'gif',
					'pdf'
				);
				
				$file	=	$this->getRequest('file');
				if(empty($file))
					return;
				
				$file	=	NBR_ROOT_DIR.DS.trim($this->unmask($file),DS);
				$ext	=	pathinfo($file,PATHINFO_EXTENSION);
				
				if(is_file($file) && in_array($ext,$filter)) {
					$this->getFile($file);
					exit;
				}
			}
	}