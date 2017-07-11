<?php
namespace Nubersoft\Flags;

use \Nubersoft\nFileHandler as nFileHandler;

class	Controller extends \Nubersoft\nApp
	{
		private	$flagPath;
		
		public	function flagName($name,$path = false)
			{
				if(empty($path))
					$path	=	NBR_ROOT_DIR;
				
				$this->flagPath	=	$this->toSingleDs($path.DS."{$name}.flag");
				
				return $this;
			}
		
		public	function getFlagPath()
			{
				return $this->flagPath;
			}
		
		public	function flagExists($name = false,$path = false)
			{
				if(!empty($name))
					$this->flagName($name,$path);
				
				return (is_file($this->getFlagPath()));
			}
		
		public	function getFlag($name,$path = false)
			{
				$file	=	$this->flagName($name,$path)->getFlagPath();
				
				return (is_file($file))? $file : false;
			}
		/*
		**	@description	Retrieve the contents of a flag (possible instructions contained with)
		*/
		public	function getFlagContents()
			{
				$flag	=	$this->getFlagPath();
				
				if(!is_file($flag))
					return false;
				
				return file_get_contents($flag);
			}
		/*
		**	@description	Static alias to flagExists()
		*/
		public	static	function hasFlag($name,$path = false)
			{
				$Flagger	=	new Controller();
				$flag		=	$Flagger->flagName($name,$path);
				
				if($Flagger->flagExists())
					return array("content"=>$Flagger->getFlagContents());
					
				return false;
			}
		/*
		**	@description	Creates a flag
		*/
		public	function createFlag($name,$content = false,$path = false)
			{
				# Save name/path to interal var
				$this->flagName($name,$path);
				# Get the file handler engine
				$Files	=	new nFileHandler();
				# Write the flag to disk
				$Files->writeToFile(array(
					'save_to'=>$this->getFlagPath(),
					'content'=>$content,
					'secure'=>false,
					'overwrite'=>true
				));
				# Let user know if file was successful
				return $this->flagExists($name,$path);
			}
		/*
		**	@description	Static alias to createFlag()
		*/
		public	static	function create($name,$content = false,$path=false)
			{
				$Flagger	=	new Controller();
				$Flagger->createFlag($name,$content,$path);
			}
		/*
		**	@description	Delete a flag if exists
		*/
		public	static	function delete($name,$path=false)
			{
				$Flagger	=	new Controller();
				$flag		=	$Flagger->flagExists($name,$path);
				
				if($flag)
					return unlink($Flagger->getFlagPath());
					
				return true;
			}
	}