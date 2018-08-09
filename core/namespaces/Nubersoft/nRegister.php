<?php
namespace Nubersoft;

class nRegister extends \Nubersoft\nApp
{
	private	$data;
	private	static	$isParsed;

	/*
	** @description - Load registry file into xml object (if possible)
	*/
	private	function getXML($get = false,$name = false)
	{
		if(defined('NBR_ROOT_DIR') && !empty($name))
			$name	=	str_replace(NBR_ROOT_DIR,'',realpath($name));

		$addName	=	$this->regParsedLocation($name);
		$lName		=	$addName->file_path;

		if($addName->parsed) {
			$parsed	=	$this->toArray($this->getDataNode('configs'));
			if(!empty($parsed[$lName]))
				return $parsed[$lName];
		}

		$name		=	(empty($name))? '' : " (found: `{$name}`)";
		$get		=	trim($get);

		if(!$xml = @simplexml_load_string($get)) {
			throw new \Exception("There was an error processing xml contents{$name}. Check your xml carefully!");
		}

	//	$conf	=	$this->toArray($this->getDataNode('xml_add_list'));

	//	if(empty($conf) || (!empty($conf) && !in_array($lName,$conf))) {				
			# Save to list
	//		$this->saveSetting('xml_add_list',array($lName));
			# Save config to setting
	//		$this->saveSetting('configs',array($lName => $xml));
	//	}
		# return xml
		return (!empty($xml))? $xml : false;
	}

	public	function parseXmlFile($file)
	{
		if(!is_file($file))
			throw new nException('File not found: '.$file);

		return $this->parseXmlString(file_get_contents($file),$file);
	}

	public	function parseXmlString($get,$name)
	{
		return $this->toArray($this->getXML($get,$name));
	}

	public	function doParse($filename)
	{
		# Try and get contents of file
		$get		=	@file_get_contents($filename); 
		# Try to process it with xml processor
		return $this->getXML($get,$filename);
	}

	public	function getRegFile($filename = false)
	{
		$filename	=	(!empty($filename) && is_file($filename))? $filename : false;

		if(empty($filename))
			return false;

		# Get the root version of the reg file
		$added		=	$this->regParsedLocation($filename);
		try {
			# Try to process it with xml processor
			$data		=	$this->doParse($filename);
		} catch (\Exception $e) {
			if($this->isAdmin())
				$this->saveError('xml_processor',array('success'=>false,'message'=>$e->getMessage()));
		}
		# If data is not empty, convert to an array
		return (!empty($data))? $this->toArray($data) : false;
	}
	/*
	**	@description	This method will parse a file or recursively search a folder and parse found xml
	**	@param	$dir	[string]	File/folder path
	**	@param	$check	[bool]	Will return true or false if list is available
	*/
	public	function parseRegFile($dir = false, $check = false)
	{
		# Set data array
		$this->data	=	false;
		# If the current include is not a directory
		if(!is_dir($dir)) {
			# If it is also not a file, stop
			if(!is_file($dir))
				return false;
			# If the current include is file, parse it
			$this->parseXmlDoc($dir);
			# Return the data array
			return $this->data;
		}
		# This needs to not filter through all folders if looking at client
		# May time out, too many folders to dive through
		if(trim($dir,DS) == trim(NBR_CLIENT_DIR,DS)) {
			$plugs		=	$this->getDirList(array('dir'=>NBR_CLIENT_DIR.DS.'plugins','type'=>array('xml')));
			$temps		=	$this->getDirList(array('dir'=>NBR_CLIENT_TEMPLATES,'type'=>array('xml')));
			$configs	=	[];
			if(!empty($plugs['list']))
				$configs	=	array_merge($plugs['list'],$configs);
			if(!empty($temps['list']))
				$configs	=	array_merge($temps['list'],$configs);
			
			foreach(scandir(NBR_CLIENT_SETTINGS) as $fileDir) {
				if(is_file(NBR_CLIENT_SETTINGS.DS.$fileDir)) {
					if(strtolower(pathinfo(NBR_CLIENT_SETTINGS.DS.$fileDir,PATHINFO_EXTENSION)) != 'xml') {
						continue;
					}
					$configs['list'][]	=	NBR_CLIENT_SETTINGS.DS.$fileDir;
				}
				else {
					
					if(!in_array($fileDir,['blockflows','actions','workflows','register']))
						continue;
					else {
						$new	=	$this->getDirList(array('dir'=>NBR_CLIENT_DIR.DS.$fileDir,'type'=>array('xml')));
						if(!empty($new['list']))
							$configs	=	array_merge($new['list'],$configs);
					}
				}
			}
		}
		else
			# Fetch options
			$configs	=	$this->getDirList(array('dir'=>$dir,'type'=>array('xml')));
		# If there is a list available
		if(!empty($configs['list'])) {
			if($check)
				return true;
			foreach($configs['list'] as $includes) {
				$this->parseXmlDoc($includes);
			}
			return (!empty($this->data))? $this->data : array();
		}
	}
	/*
	**	@description	This method will take a path and parse the location if file is valid
	**	@param	$includes	[string]	This is the file path to the xml
	*/
	public	function parseXmlDoc($includes)
	{
		if(!is_file($includes))
			return false;
		# Check if there is already registered value associated with the parse
		$xmlFile	=	$this->regParsedLocation($includes);
		# If it's already been parsed
		if($xmlFile->parsed) {
			# Get the key
			$getConfigs	=	$this->getDataNode('configs');
			# Set the key, then continue on
			if(isset($getConfigs->{$xmlFile->file_path}))
				$this->data[$xmlFile->file_path]	=	$getConfigs->{$xmlFile->file_path};
			else
				return false;
		}
		else {
			$parseReg	=	$this->getRegFile($includes);

			if(empty($parseReg))
				return false;

			$this->data[$xmlFile->file_path]	=	$parseReg;
		}
	}

	public	function regParsedLocation($filename)
	{
		$parsed		=	false;
		# Get the root version of the reg file, lowercase, remove separator
		$added		=	strtolower(str_replace(array(NBR_ROOT_DIR,DIRECTORY_SEPARATOR),'',$filename));
		# Remove extension
		$added		=	trim($added,'.xml');
		# Get the list of already added regs
		$addList	=	$this->toArray($this->getDataNode('xml_add_list'));
		# Check if this is already parsed
		if(!empty($addList)) {
			if(in_array($added,$addList)) {
				$parsed	=	true;

			}
		}
		return (object) array('parsed'=>$parsed,'file_path'=>$added);
	}
}