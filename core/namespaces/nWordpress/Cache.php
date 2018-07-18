<?php
namespace nWordpress;

use \nWordpress\User;

class Cache extends \Nubersoft\nCache
{
	private	$data,
			$root_path;
	
	public	function capture($func)
	{
		ob_start();
		echo $func();
		$this->data	=	ob_get_contents();
		ob_end_clean();
		
		return $this;
	}
	
	public	function get()
	{
		return $this->data;
	}
	
	public	function createCacheFile($content)
	{
		if($this->isDir(pathinfo($this->hasLayout,PATHINFO_DIRNAME)))
			file_put_contents($this->hasLayout,$content);
		else
			trigger_error('Could not create directory.',E_USER_NOTICE);

		return $this;
	}
	
	public	function getStandardPath($appendPath = false,$cou = 'USA',$func = false)
	{
		$User	=	new User();
		$vars	=	[
			$this->root_path,
			get_locale(),
			date('Y-m-d'),
			$User->isLoggedIn(),
			$User->isAdmin()
		];
		
		$file	=	(is_callable($func))? $func($vars,$this).DS.$appendPath : implode(DS,$vars).$appendPath;
		return str_replace(DS.DS,DS,$file);
	}
	
	public	function setCacheRoot($path)
	{
		$this->root_path	=	$path;
		return $this;
	}
}