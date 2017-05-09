<?php
namespace nPlugins\Nubersoft;

class Cache extends \Nubersoft\nCache
	{
		public	function runStaticSite()
			{
				# If an action occurs, don't use cache
				if(!empty($this->getRequest('action')))
					return;
				$page	=	$this->getPageURI();
				if(empty($page))
					return;
				elseif(count($page) == 1)
					return;
				elseif($this->getPageURI('is_admin') == 1)
					return;
				$is_cached	=	($this->getPageURI('auto_cache') == 'on');
				# Directory where the cache is saved to
				$file		=	$this->getSaveLocation(DS.'index.html');
				# Run render
				if($is_cached && $this->allowCacheRead()) { 
					if(is_file($file)) {
						if(!$this->isAdmin()) {
							$this->getHelper('NubeData')->destroy();
							echo $this->render($file);
							exit;
						}
					}
				}
			}
		
		public	function startCache()
			{
				$is_cached	=	($this->getPageURI('auto_cache') == 'on');
				# Stop if not cached
				if(!$is_cached)
					return;
				# Check htaccess
				$this->htaccessCheck($this->getCacheFolder());
				# Get cache path
				self::$path	=	$this->getSaveLocation(DS.'index.html');
				# Stop
				if(is_file(self::$path) && $this->allowCacheRead())
					return;
				# Start the buffer
				if(!$this->isAdmin())
					ob_start();
			}
		
		public	function endCache()
			{
				$is_cached	=	($this->getPageURI('auto_cache') == 'on');
				# Stop if not cached
				if(!$is_cached)
					return;
				
				if(!empty(self::$path)) {
					if(!is_file(self::$path)) {
						if(!$this->isAdmin() && $this->allowCacheRead()) {
							$data	=	ob_get_contents();
							ob_end_clean();
							
							echo $data;
							
							if($this->isDir(pathinfo(self::$path,PATHINFO_DIRNAME)))
								file_put_contents(self::$path,$data);
							else
								trigger_error('Could not page cache.',E_USER_NOTICE);
						}
					}
				}
			}
		
		public	function buildStaticCache()
			{
				if(!$this->isAdmin())
					return;
				
				$cURL	=	$this->getHelper('cURL');
				$cURL->emulateBrowser();
				$pages	=	$this->nQuery()->query("select CONCAT('".str_replace('https://','http://',$this->siteUrl())."',`full_path`) as path from main_menus where `page_live` = 'on'")->getResults();
				
				$new	=	array();
				$this->extractAll($pages,$new);
				foreach($new as $path) {
					echo $cURL->connect('http://www.networksolutions.com',false,false);
					
					die($path);
				}
				
				die(printpre($new));
			}
		/*
		**	@description	Returns the cache queueiug path
		*/
		public	function getCacheQueuePath()
			{
				return NBR_CLIENT_SETTINGS.DS.'cache_start.pref';
			}
		/*
		**	@description	Create a file to indicate the caching process needs to draw
		**					from live while file is present
		*/
		public	function setCacheBypass()
			{
				$file	=	$this->getCacheQueuePath();
				if(is_file($file))
					return true;
				
				return (file_put_contents($file,true) !== false);
			}
		/*
		**	@description	Remove file to indicate the delete cache process has ended
		*/
		public	function removeCacheBypass()
			{
				$file	=	$this->getCacheQueuePath();
				if(is_file($file)) {
					return (@unlink($file));
				}
				
				return true;
			}
		/*
		**	@description	Checks to see if the settings should draw from live data or not
		*/
		public	function allowCacheRead()
			{
				if(!is_file($this->getCacheQueuePath()))
					return true;
			}
	}