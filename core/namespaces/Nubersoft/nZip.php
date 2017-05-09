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
			
		public	function createZipFile($path)
			{
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
						if(pathinfo($file,PATHINFO_BASENAME) != $base)
							$this->addFile($file,$this->nApp->stripRoot($file));
					}
				}
				
				$this->close();
			}
			
		public	function deploy()
			{
				if(!$this->nApp->isAdmin())
					return;
				
				ini_set('max_execution_time',2000000000);
				
				$zip	=	NBR_ROOT_DIR.DS.date('YmdHis').'_nsoft.zip';
				$this->getFolderContents(NBR_ROOT_DIR)->createZipfile($zip);
				
				if(is_file($zip)) {
					$this->nApp->getPlugin('\nPlugins\Nubersoft\CoreDownloader')->getFile($zip);
					unlink($zip);
					exit;
				}
				else {
					throw new \Exception('Couldn\'t work');
				}
			}
	}