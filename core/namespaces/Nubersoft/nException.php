<?php
namespace Nubersoft;

class nException extends \Exception
{
	private	$opts,
			$extractArr;
	/**
	*	@description	Alias of saveToLog()
	*/
	public	function toLog($filename = false, $path = 'reporting')
	{
		$this->saveToLog($filename,$path);
		return $this;
	}
	
	public	function saveToLog($filename = false, $path = 'reporting')
	{
		if(!defined('DS'))
			define('DS',DIRECTORY_SEPARATOR);
		# Site root
		$NBR_ROOT_DIR		=	realpath(__DIR__.DS.'..'.DS.'..'.DS.'..');
		# Reporting path
		$savePath			=	$NBR_ROOT_DIR.DS.'client'.DS.'settings'.DS.trim($path,DS);
		# Make the reporting directort
		if(!is_dir($savePath))
			mkdir($savePath,0755,true);
		# Get the read-only access
		$htaccessFile		=	__DIR__.DS.'nReWriter'.DS.'serverRead'.DS.'htaccess.txt';
		# Write the access file to the reporting path
		file_put_contents($savePath.DS.'.htaccess',$htaccessFile);
		# Create/Use filename
		$filename			=	(!empty($filename))? $filename.'.log' : $savePath.DS.date('Ymd_His').'.log';
		$this->extractArr	=	(!empty($this->extractArr))? $this->extractArr : false;
		$this->opts			=	(!empty($this->opts))? $this->opts : false;
		# Create the autoloader
		if(!function_exists('nloader')) {
			# Include autoloader function
			include_once($NBR_ROOT_DIR.DS.'core'.DS.'functions'.DS.'nloader.php');
			# Create the autoloader
			spl_autoload_register('nloader');
		}
		# Save the log file
		(new nLogger())->toFile($this->getMessage(),$filename);
	}

	public	function setOptions($array)
	{
		$this->opts	=	$array;
		return $this;
	}

	public	function checkConfig($val)
	{
		$find				=	array('logging');
		$this->extractArr	=	(is_array($val))? array_merge($find,$val) : array_push($find,$val);
		return $this;
	}
	/**
	*	@description	This checks if the two important pieces of a standard install are present
	*/
	public	function hasWorkingInstall()
	{
		$DS		=	DIRECTORY_SEPARATOR;
		$ROOT	=	realpath(__DIR__.$DS.'..'.$DS.'..'.$DS.'..');
		if(!defined('NBR_ROOT_DIR')) {
			$ROOT.$DS.'defines.php';
			if(is_file($defines))
				include_once($defines);
		}
		# Check if there is a base config and a client regeistry file
		return (defined('NBR_CLIENT_DIR') && is_file($ROOT.$DS.'client'.$DS.'settings'.$DS.'registry.xml'));
	}
	/**
	*	@description	Run either a warning or error based function when an error is thrown
	*/
	public	function doAction($warning,$error)
	{
		# Make sure the two params are anonymous functions
		if(!is_callable($warning) || !is_callable($error))
			throw new \Exception('This error method requires two anon. functions.');
		# Check if the error code has a 2*** based number
		return (preg_match('/^2.*/',$this->getCode()))? $warning($this) : $error($this);
	}
}