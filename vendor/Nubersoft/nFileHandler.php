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
			
		}
	}
	/**
	 *	@description	
	 */
	public	function recurseDelete($path)
	{
		$recurse	=	new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS));
		
		foreach($recurse as $filepath => $it) {
			unlink($filepath);
			$dir	=	pathinfo($filepath, PATHINFO_DIRNAME);
			if(is_dir($dir)) {
				if(count(scandir($dir)) == 2) {
					rmdir($dir);
				}
			}
		}
	}
}