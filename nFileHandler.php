<?php
namespace Nubersoft;
/**
 *	@description	
 */
class nFileHandler extends \Nubersoft\nApp
{
	private	$target	=	[];
	/**
	 *	@description	
	 */
	public	function addTarget($path)
	{
		$this->target[]	=	$path;
		return $this;
	}
	/**
	 *	@description	
	 */
	public	function deleteAll($path = false)
	{
		if(!empty($path))
			$this->addTarget($path);
		
		foreach($this->target as $target) {
			$this->recurseDelete($target);
		}
	}
	/**
	 *	@description	
	 */
	public	function recurseDelete($path)
	{
		if(!is_dir($path) && !is_file($path))
			return false;
		
		$isDir		=	is_dir($path);
		$recurse	=	new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::KEY_AS_PATHNAME | \RecursiveDirectoryIterator::SKIP_DOTS));
		
		foreach($recurse as $filepath => $it) {
			
			if(is_file($filepath))
				unlink($filepath);
			
			$dir	=	pathinfo($filepath, PATHINFO_DIRNAME);
			if(is_dir($dir)) {
				if(count(scandir($dir)) == 2) {
					rmdir($dir);
				}
			}
		}
		
		if($isDir)
			$this->isDir($path, 1);
	}
}