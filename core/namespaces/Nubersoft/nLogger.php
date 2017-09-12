<?php
namespace Nubersoft;

class nLogger extends \Nubersoft\nApp
	{
		private	$attr,
				$path;
		
		public	function setSavePath($path)
			{
				$this->path	=	$path;
				return $this;
			}
		
		public	function __call($name,$attr = false)
			{
				# Shorter sep name
				$ds		=	(!defined('DS'))? DIRECTORY_SEPARATOR : DS;
				$match	=	(!empty($attr[0]['match']))? $attr[0]['match'] : false;
				# Set path
				$this->path	=	(empty($this->path))? NBR_CLIENT_DIR.$ds.'settings'.$ds.str_replace('_',$ds,strtolower($name)) : str_replace($ds.$ds,$ds,$this->path.$ds.str_replace('_',$ds,strtolower($name)));
				
				$fName	=	array(
								"path"=>rtrim($this->path,basename($this->path)),
								"filename"=>basename($this->path).'.txt'
							);
							
				$msg	=	$attr[0]['content'];
				$opts	=	array(
								'skip_post'=>true,
								'mode'=>'r+'
							);
				
				$this->saveToLog($fName,$msg,$match,$opts);
				
				if(!empty($attr[0]['headers']) && is_array($attr[0]['headers'])) {
					foreach($attr[0]['headers'] as $header)
						header($header);
				}
				
				if(!empty($attr[0]['die']))
					die($attr[0]['die']);
				
				return $this;
			}
		
		public	function saveToLog($filename = false,$message = false,$opts = false,$writeOpts = array('secure'=>true))
			{
				if(!$filename || !$message)
					return false;
				# If there are options
				elseif(is_array($writeOpts)) {
					# If there is a skip on $_POST
					if(isset($writeOpts['skip_post'])) {
						# If there is a post, return
						if(!$writeOpts['skip_post']) {
							if(empty($this->getPost()))
								return;
						}
					}
				}
				
				# Set defaults for method
				$saveLog	=	true;
				$logOpts	=	false;
				$max		=	$this->getByteSize('5',array('from'=>'MB','to'=>'B'));
				$class		=	"nFileHandler";
				$path		=	(!empty($this->getCacheFolder()))? $this->getCacheFolder() : NBR_CLIENT_DIR.DS.'settings'.DS.'tempCache'.DS;
				$writeType	=	'JSON';
				if(is_array($filename)) {
					if(isset($filename['path']))
						$path		=	$filename['path'];
					if(isset($filename['filename']))
						$filename	=	$filename['filename'];
					
					if(!is_string($filename)) {
						throw new \Nubersoft\nException('Filename invalid');
						return false;
					}
				}
				# See if there are any write options
				if(is_array($opts)) {
					$getKey	=	end($opts);
					# See if log option is set
					$logArr		=	$this->getMatchedArray($opts);
					# If it is set
					if(!empty($logArr[$getKey][0]['on'])) {
						# Extract the setting
						$saveLog	=	$this->getBoolVal($logArr[$getKey][0]['on']);
						# If log not allowed stop
						if(!$saveLog)
							return false;
						$logOpts	=	$logArr[$getKey][0];
						# Reset defaults
						if(!empty($logOpts['filename']))
							$filename	=	$logOpts['filename'];
						# Use a class other than default
						if(!empty($logOpts['class']))
							$class		=	$logOpts['class'];
						# Max filesize
						if(!empty($logOpts['max_size'])) {
							$maxSize	=	$logOpts['max_size'];
							$max		=	$this->getByteSize($maxSize,array('to'=>'B'));
						}
						if(!empty($logOpts['write_type'])) {
							$writeType	=	($logOpts['write_type'] == 'JSON');
						}
					}
				}
				# Get path to temp folder
				$path		=	$this->toSingleDs(DS.$path.DS.$filename);
				if($writeType) {
					# Data to json encode
					$encode		=	array(
										'debug'=>debug_backtrace(),
										'message'=>$message
									);
				}
				
				$message	=	(!$writeType && is_array($message))? implode(". ",$message) : $message;
				
				# Save all to settings
				$settings	=	array(
									"save_to"=>$path,
									"content"=>(($writeType)? json_encode($encode) : $message)
								);
				# These should be objects to pass write settings
				if(!empty($writeOpts) && is_array($writeOpts))
					$settings	=	array_merge($settings,$writeOpts);
				# If the file is already a file
				if(is_file($path)) {
					# If the current file and the contente of new is greater than max
					if((filesize($path)+strlen($settings['content'])) > $max) {
						# Rename the current
						rename($path,$path.date('YmdHis').'_'.bin2hex(mt_rand(1000,9999).mt_rand(1000,9999)).'.ARCHIVE');
					}
				}
				# Write to file
				self::call($class)->writeToFile($settings);
			}
			
		public	function save($array,$path,$pointer = 'r+')
			{
				$path	=	(is_array($path))? NBR_ROOT_DIR.DS.implode(DS,$path) : $path;
				$str	=	'';
				foreach($array as $key => $value) {
					$value	=	(is_array($value) || is_object($value))? json_encode($value) : $value;
					$str	.=	$key.": ".ltrim("[{$value}]").PHP_EOL;
				}
				$dirPath	=	pathinfo($path,PATHINFO_DIRNAME);
				if(!is_dir($dirPath))
					@mkdir($dirPath,0755,true);
	
				if(is_dir($dirPath)) {
					$this->getHelper('nFileHandler')->writeToFile(array(
						'content'=>$str.'-----------------------------------'.PHP_EOL,
						'save_to'=>$path,
						'secure'=>true,
						'type'=>$pointer
					));
				}
			}
		
		public	function toFile($msg,$path)
		{

			if($this->isDir(pathinfo($path,PATHINFO_DIRNAME)))
			   return file_put_contents($path,$msg,FILE_APPEND);
			
			return false;
		}
	}