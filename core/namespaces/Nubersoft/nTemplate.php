<?php
namespace Nubersoft;

class nTemplate extends \Nubersoft\nApp
	{
		protected	$siteValue,
					$Methodizer;
		
		public	function getFrontEnd($file = false)
			{
				return $this->determinePlace('frontend',$file);
			}
			
		public	function getBackEnd($file = false)
			{
				return $this->determinePlace('admintools',$file);
			}
		/*
		**	@description	Alias of determinePlace()
		*/
		public	function getTemplateFrom($type,$file = false)
			{
				return $this->determinePlace($type,$file);
			}
		/*
		**	@description	Searches available template areas and tries to find the first match
		**	@param	$type	[string]	This is the core directory folder (example "plugins")
		**	@param	$file	[string | bool(empty)]	This is any appended file
		*/
		public	function determinePlace($type,$file = false)
			{
				# Get the methodizer engine
				$this->Methodizer	=	$this->getStored();
				# Trim off the file
				$file		=	trim(trim($file),DS);
				# Fetches all the template areas
				$templates	=	$this->toArray($this->getSite('templates'));
				# Save the template array to the methodize
				$this->Methodizer->saveAttr('determine_place_templates',$templates);
				# Loop through all the available templates
				foreach($templates as $spot) {
					# Create final complied path
					$template		=	$this->toSingleDs($spot['dir'].DS.$type.DS.$file);
					# Create final absolute path
					$fileDir		=	$this->toSingleDs(NBR_ROOT_DIR.DS.$template);
					# Check if final is a directory
					$is_dir			=	is_dir($fileDir);
					# Store 
					$tempDirStr[]	=	$fileDir;
					$this->Methodizer->saveAttr('determine_place',$tempDirStr);
					# If any of the template sources match either a file or folder, return that
					if($is_dir || is_file($fileDir))
						return str_replace(NBR_ROOT_DIR,'',$fileDir);
				}
			}
		
		public	function getStored()
			{
				return (!($this->Methodizer instanceof \Nubersoft\Methodize))? $this->getHelper('Methodize') : $this->Methodizer;
			}
		
		public	function setDeterminer($key,$value)
			{
				$this->setValue[$key]	=	$value;
				return $this;
			}
		
		public	function determinerIsSet($key,$value)
			{
				if(isset($this->setValue[$key])) {
					$val	=	$this->setValue[$key];
					unset($this->setValue[$key]);
					return $val;
				}
				
				return $value;
			}
	}