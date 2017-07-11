<?php
namespace Nubersoft;

class nZip extends \ZipArchive
	{
		protected	$dirList,
					$fileList,
					$nApp,
					$zFile;
		
		public	function __construct($nApp)
			{
				$this->nApp	=	$nApp;
			}
		
		public	function getFolderContents($dir)
			{
				$opt		=	\RecursiveIteratorIterator::CHILD_FIRST;
				$iterator	=	new \RecursiveIteratorIterator(
									new \RecursiveDirectoryIterator($dir),$opt
								);
				
				foreach($iterator as $file) {
					if(is_dir($file))
						$this->dirList[]	=	$file->getPathName();
					elseif(is_file($file))
						$this->fileList[]	=	$file->getPathName();
				}
				
				if(!empty($this->dirList)) {
					$this->dirList	=	array_reverse($this->dirList);
				}
				
				return $this;
			}
			
		public	function createZipFile($path,$filter = false)
			{
				if(empty($filter) || !is_array($filter))
					$filter	=	['php','xml','json','pref','log','txt','js','css','htaccess'];
					
				$opened	=	$this->open($path, \ZipArchive::CREATE);
				$base	=	pathinfo($path,PATHINFO_BASENAME);
				if($opened !== true) {
					throw new \Exception('Deploy failed.');
				}
				
				if(!empty($this->dirList)) {
					foreach($this->dirList as $dir) {
						$this->addEmptyDir(ltrim($this->nApp->stripRoot($dir),'/'));
					}
				}
				
				if(!empty($this->fileList)) {
					foreach($this->fileList as $file) {
						$pathinfo	=	pathinfo($file);
						if($pathinfo['basename'] != $base) {
							if(empty($pathinfo['extension']))
								continue;
							
							$ext	=	strtolower($pathinfo['extension']);
							if(in_array($ext,$filter) && is_file($file))
								$this->addFile($file,$this->nApp->stripRoot($file));
							else {
								if(!is_file($file))
									die($file);
							}
						}
					}
				}
				
				$this->close();
			}
			
		public	function deploy()
			{
				if(!$this->nApp->isAdmin())
					return;
				# Allow the process to take a very long time
				ini_set('max_execution_time',2000000000);
				# Create a save path
				$zip	=	NBR_CLIENT_DIR.DS.'settings'.DS.'backups'.DS.str_replace('.','_',$this->nApp->siteHost()).'_'.date('YmdHis').'_nsoft.zip';
				# Create the base directory
				if(!$this->nApp->isDir(pathinfo($zip,PATHINFO_DIRNAME))) {
					$msg	=	'Couldn\'t make save folder';
					$this->toAlert($msg);
					throw new \Exception($msg);
				}
				# Get the contents of the root and create a zip file
				$this->getFolderContents(NBR_ROOT_DIR)->createZipfile($zip);
				# If the file is created, download it
				if(is_file($zip)) {
					# Download the file
					(new \nPlugins\Nubersoft\CoreDownloader())->getFile($zip,array(
						"Cache-Control"=>"must-revalidate, post-check=0, pre-check=0",
						'Content-type:'=>'application/zip',
						'Content-Transfer-Encoding'=>'binary',
						'Connection'=>'Keep-Alive',
						'Expires'=>'0',
						'Pragma'=>'public',
						'Content-length: '.filesize($zip),
						'Content-disposition'=>'attachment; filename="'.basename($zip).'"'
					));
					exit;
				}
				# 
				else {
					$msg	=	'Couldn\'t create the required backup.';
					$this->toAlert($msg);
					throw new \Exception($msg);
				}
			}
		
	}