<?php
namespace Nubersoft\nFileHandler;

class Controller extends \Nubersoft\nFileHandler\Model
{
	/*
	**	@description	Saves and fetches from the settings folder, not the cache folder.
	**	@param	$name [string]	Name of the file that is being saved/retrieved
	**	@param	$func	[function | any]	Callable function or data being saved to file
	**	@param	$ext	[string]	The file extension being save/extracted
	**	@param	$path	[string|bool(false)]	This is the path where the file will be saved from/to
	*/
	public	function getSettingsFile($name,$func,$ext = 'json',$path = false)
	{
		$ext	=	(empty($ext))? 'json' : $ext;
		$path	=	(!empty($path) && is_dir($path))? $path : NBR_CLIENT_SETTINGS.DS.'preferences';
		$file	=	$path.DS.$name.'.'.$ext;
		# Check if there is a cache pause on
		$allow	=	$this->nCache()->allowCacheRead();
		# Save htaccess file
		if($this->isDir($allow,false) && !is_file($htaccess = $path.DS.'.htaccess'))
			file_put_contents($htaccess,$this->getHelper('nReWriter')->getScript('serverReadWrite'));

		if(is_file($file) && $allow) {
			return ($ext == 'json')? json_decode(file_get_contents($file),true) : file_get_contents($file);
		}
		# Process
		$contents	=	(is_callable($func))? $func($this,$file) : $func;
		# Make save directory
		if(!$this->isDir($path))
			trigger_error("Path ({$path}) could not be saved for perminant storage.");
		if(is_array($contents) || is_object($contents))
			$contents	=	json_encode($contents);
		# Save file
		if(!$this->isAjaxRequest() && $allow)
			file_put_contents($file,$contents);
		# Return the contents for use
		return $contents;
	}

	public	function getUploadDir($table,$settings = false)
	{
		$table		=	(!empty($table))? trim($table) : false;
		$append		=	(!isset($settings['append']) || !empty($settings['append']));
		$default	=	(!empty($settings['dir']))? $settings['dir'] : '/client/images/default/';

		if(empty($table))
			return $default;

		$dir	=	nquery()	->select("file_path")
								->from("upload_directory")
								->where(array("assoc_table"=>$table))
								->fetch();

		$path	=	($dir != 0)? $dir[0]["file_path"] : $default;

		return ($append)? str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.$path) : $path;
	}
}