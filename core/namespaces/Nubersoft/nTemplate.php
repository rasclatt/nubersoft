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
			
			if(!is_array($templates))
				throw new nException('An application error occurred, templates failed to load.');
			
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
		
		public	function getDefaultTemplate()
		{
			$pref		=	$this->toSingleDs($this->getSettingsDir('template.pref'));
			$default	=	DS.'core'.DS.'template'.DS.'default';
			if(is_file($pref))
				return (is_file($pref))? @file_get_contents($pref) : false;

			if(empty($this->getDataNode('preferences')))
				(new GetSitePrefs)->set();

			$getTemp	=	$this->getMatchedArray(array(
				'settings_site',
				'content',
				'template_folder'
			),'',$this->toArray($this->getDataNode('preferences')));

			$template	=	(!empty($getTemp['template'][0]))? trim($this->toSingleDs(DS.$getTemp['template'][0]),DS) : $pref;
			if(!is_dir(NBR_ROOT_DIR.DS.$template))
				$template	=	$default;

			$this->getHelper('nFileHandler')->writeToFile(array(
				'content'=>$template,
				'save_to'=>$pref,
				'overwrite'=>true,
				'secure'=>true
				));

			return $template;
		}
		
		public	function getTemplatePathMatch($path,$dirType = 'frontend',$array = false)
		{
			$templates	=	(!empty($array))? $array : $this->toArray($this->getDataNode('site')->templates);

			if(!is_array($templates))
				return false;

			foreach($templates as $type) {
				if(is_file($file = NBR_ROOT_DIR.$type[$dirType].DS.$path))
					return $file;
				elseif(is_dir($dir = NBR_ROOT_DIR.$type[$dirType].DS.$path))
					return $dir;
			}

			return false;
		}
		
		protected	function createComponent($type,$data,$refanchor = 'ntemplate',$refpage = false, $parent = false)
		{
			$comp['ref_anchor']	=	$refanchor;
			$comp['content']	=	$data;
			$comp['ref_spot']	=	$type;
			$comp['ref_page']	=	(!empty($refpage))? $refpage : $this->getPageURI('unique_id');
			$comp['parent_id']	=	(!empty($parent))? $parent : false;

			$this->getPlugin('\nPlugins\Nubersoft\CoreDatabase')->addComponent($comp);
		}
	
		public	static	function getFileFromDefaultTemplate($file)
		{
			return $this->toSingleDs(__DIR__.DS.'core'.DS.'template'.DS.'default'.DS.$file);
		}
	}