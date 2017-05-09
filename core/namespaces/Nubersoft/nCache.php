<?php
namespace Nubersoft;

class	nCache extends \Nubersoft\nApp
	{
		protected	$hasLayout,
					$hasCache;
		
		protected	static	$path;
		
		public	function app()
			{
				return $this->getHelper('BuildCache');
			}
		
		public	function htaccessCheck($htaccess)
			{
				if(!is_file($htaccess.DS.'.htaccess'))
					nReWriter::serverRead(array('write'=>true,'dir'=>$htaccess));
			}
		
		public	function getSaveLocation($append = false)
			{
				# Checks if status is on
				$status		=	($this->getPageURI('session_status') == 'on' && $this->isLoggedIn())? 1:2;
				# Directory where the cache is saved to
				return $this->toSingleDs(DS.$this->getStandardPath().DS.$status.$append);
			}
		
		public	function setHasCache($path)
			{
				$this->hasCache	=	false;
				
				if(is_file($path))
					$this->hasCache	=	true;
				
				return $this;
			}
		/*
		**	@description	Start the simple cache method
		**	@param $path [string]	This is the path where the cachefile is to be found/saved
		*/
		public	function cacheBegin($path)
			{
				# Stores if a cache path is set
				$this->hasLayout	=	$path;
				# Set cache
				$this->setHasCache($path);
				
				ob_start();
				return $this;
			}
		/*
		**	@description	Checks if the path is empty or not.
		**					If empty, means the file is not saved in the path from cacheBegin() method.
		*/
		public	function isCached()
			{
				return (!empty($this->hasCache));
			}
		/*
		**	@description	Ends the cache, saves the content, returns the content for viewing
		**					OR if file already exists, it will include the cache page for render
		*/
		public	function cacheRender()
			{
				# Include the file if there is a valid path
				if($this->isCached())
					include($this->hasLayout);
				
				$data	=	ob_get_contents();
				ob_end_clean();
				
				# If the file was not already a cache file
				if(!$this->isCached())
					# Save the file to disk
					$this->createCacheFile($data);
				# Return the contents of the buffer
				return $data;
			}
		/*
		**	@description	Saves the cache to file
		**	@param $content [string]	This is the content of the file that will be saved to
		*/
		public	function createCacheFile($content)
			{
				if($this->isDir(pathinfo($this->hasLayout,PATHINFO_DIRNAME)))
					$this->saveFile($content,$this->hasLayout);
				else
					throw new \Exception('Could not create directory.');
			}
	}