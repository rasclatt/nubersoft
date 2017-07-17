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
		/*
		**	@description	Creates a standard cache path for saving cached elements into the cache folder
		**	@param	$appendPath	[string|empty]	Self-explanitory
		**	@param	$cou	[string]	This is the default locale
		**	@param	$func	[anon func|empty]	This can be a callable function to process a new cache path
		*/
		public	function getStandardPath($appendPath = false,$cou = 'USA',$func = false)
			{
				$cacheDir	=	$this->getCacheFolder().DS.'pages'.DS;
				$host		=	trim(str_replace(array('.','-','\_','http://','https://'),array(''),$this->localeUrl()),'/');
				$state		=	(empty($this->getPageURI('is_admin')) || $this->getPageURI('is_admin') > 1)? 'base_view' : 'admin_view';
				$toggled	=	(!empty($this->getDataNode('_SESSION')->toggle->edit))? 'is_toggled' : 'not_toggle';
				$post		=	$this->getPost('action');
				$get		=	$this->getGet('action');
				$usePost	=	(is_string($post))? DS.md5($post) : '';
				$useGet		=	(is_string($get))? DS.md5($get) : '';
				$country	=	(!empty($this->getSession('LOCALE')))? trim($this->getSession('LOCALE'),'/') : $cou;
				$tempBase	=	$this->getDataNode('site')->templates->template_site->dir;
				$defPath	=	(!empty($this->getPageURI('full_path')))? trim(str_replace('/',DS,$this->getPageURI('full_path')),DS) : 'static';
				$ID			=	(!empty($this->getPageURI('ID')))? $this->getPageURI('ID') : (($defPath == 'static')? 'error' : md5($defPath));
				$loggedIn	=	($this->isLoggedIn())? 'loggedin' : 'loggedout';
				$usergroup	=	(!empty($this->getSession('usergroup')))? $this->getSession('usergroup') : 'static';
				$isSsl		=	($this->isSsl())? 'https' : 'http';
				$group_id	=	(!empty($this->getSession('group_id')))? 'gid_'.$this->getSession('group_id') : 'gid_base';
				$finalPath	=	$this->toSingleDs($cacheDir.DS.$host.DS.$country.DS.$isSsl.DS.$tempBase.DS.$defPath.DS.$loggedIn.$useGet.$usePost.DS.$toggled.DS.$state.DS.$usergroup.DS.$group_id.DS.$ID.$appendPath);
				
				if(is_callable($func))
					return $func($this,$finalPath);
				else
					return $finalPath;
			}
		/*
		**	@description	Returns the designated cache folder
		*/
		public	function getCacheFolder($append = false)
			{
				# Cache pref location
				$cache	=	$this->getSettingsDir('cache_dir.pref');
				# See if the cache has already pulled and return it
				if(!empty($this->getDataNode('site')->cache_dir)) {
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('site')->cache_dir.DS.$append),DS);
					return $cachePath;
				}
				# If there is a define, use it first
				elseif(defined('CACHE_DIR') && !empty(constant('CACHE_DIR'))) {
					$this->saveSetting('site',array('cache_dir'=>CACHE_DIR));
					# Trim the right side and remove any double forward slashes
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.CACHE_DIR.DS.$append),DS);
					return $cachePath;
				}
				# If no define exists, the try and extract the cached one
				elseif(is_file($cache)) {
					$cacheContent	=	@file_get_contents($cache);
					$this->saveSetting('site',array('cache_dir'=>$cacheContent));
					$cachePath	=	rtrim($this->toSingleDs(NBR_ROOT_DIR.DS.$cacheContent.DS.$append),DS);
					return $cachePath;
				}
				# If no define or cache file is found, create a cache file
				else {
					# Try and get the client reg file but if not found use base
					$getRegFunc	=	function() use ($append)
						{
							$path[]		=	NBR_CLIENT_SETTINGS.DS.'registry.xml';
							$path[]		=	NBR_SETTINGS.DS.'registry.xml';
							
							foreach($path as $spot) {
								if(!is_file($spot))
									continue;
									
								$reg	=	$this->getMatchedArray(array('ondefine','cache_dir'),'',$this->getHelper('nRegister')->parseXmlFile($spot));
								
								if(!empty($reg['cache_dir'][0]))
									return rtrim($this->toSingleDs($reg['cache_dir'][0].DS.$append),DS);
							}
						};
					# Run the above anon function to get the path for the cache file
					$cachePath	=	$getRegFunc();
					# Save to data node
					$this->saveSetting('site',array('cache_dir'=>$cachePath));
					# Save the pref file. Have to use this instead of savePrefFile() because it
					# runs into a loop
					$this->saveFile(rtrim($cachePath,DS),$cache);
					# Return the folder from the settings
					$cachePath	=	trim($this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDataNode('site')->cache_dir.DS.$append),DS);
					return $cachePath;
				}
			}
	}