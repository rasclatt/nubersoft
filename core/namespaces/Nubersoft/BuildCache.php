<?php
namespace Nubersoft;

class	BuildCache extends \Nubersoft\nApp
	{
		public		$fileOutput,
					$cachefile,
					$_cache,
					$endCache,
					$cacheDir,
					$cacheRoot,
					$autoCache;
		
		protected	$allow,
					$nuber;
		
		private		$data,
					$compile,
					$content;
		
		public	function __construct()
			{	
				$this->compile	=	true;
				$this->data		=	false;
				$this->autoload(array('compare','is_loggedin','is_admin'));
				return parent::__construct();
			}
		
		public	function initialize($fileOutput = 'html')
			{
				$this->_cache		=	false;
				# Checks if cache is set at all, if so make it on or false
				$this->autoCache	=	($this->compare($this->getCachedStatus(),'on'));
				# If user is allowed to view uncached (true)
				$this->allow		=	(!$this->isLoggedIn() || !$this->isAdmin());
				# If cache on
				if($this->autoCache) {
					# If not Admin or not logged in
					if($this->allow) {
						$reqfilename		=	$this->getPage("ID");
						$this->fileOutput	=	$fileOutput;
						$this->cacheRoot	=	$this->getCacheFolder();
						# Set the directory found in the cache folder to either the usergroup name<br>
						//(numeric) or the id of the page (which will be the second var)
						$this->cacheDir		=	$this->getStandardPath(false);
						# DocumentRoot/cache/$ID/$_SESSION/$ID.ext
						$this->cachefile	=	str_replace(DS.DS,DS,$this->cacheDir.DS.'index.'.$this->fileOutput);
						# If there is a cache file, include it and return a true so that the flush happens
						# Let command know it needs caching
						$this->_cache		=	(is_file($this->cachefile));
					}
				}

				return $this;
			}
			
		public	function buildCacheDir($directoryExp, $resetCachRoot,$htaccess = false)
			{
				$buildDirRoots	=	"";
				
				if(is_array($directoryExp)) {
					foreach($directoryExp as $keys => $values){
						$buildDirRoots	.=	$values.DS;
						if(!$this->isDir($resetCachRoot))
							$this->toAlert('Could not make dir: '.$resetCachRoot);
							
						if(!$this->isDir($resetCachRoot . $buildDirRoots))
							$this->toAlert('Could not make dir: '.$resetCachRoot . $buildDirRoots);
					}
				}
				else {
					$path	=	str_replace(DS.DS,DS,$resetCachRoot.DS.$directoryExp);
					if(!$this->isDir($path))
						$this->toAlert('Could not make dir: '.$path);
				}
				
				if($htaccess != false) {
					if(!is_file(str_replace(DS.DS,DS,$resetCachRoot.DS.".htaccess"))) {
						$this->getHelper('nReWriter')->createHtaccess(array("rule"=>$htaccess,"dir"=>$resetCachRoot));
					}
				}
			}
		
		public	function renderDocument($string = false)
			{
				# If is a file, include saved file
				if($this->_cache) {
					ob_start();
					include($this->cachefile);
					$this->data	=	ob_get_contents();
					ob_end_clean();
					return $this;
				}
				
				$this->data	=	$string;
				
				# If is content not empty, run the cache
				if($this->data) {
					if((isset($this->_cache) && !$this->_cache) && ($this->allow)) {
						ob_start();
						echo $string;
						$directoryExp	=	str_replace($this->cacheRoot, "", $this->cacheDir);
						$resetCachRoot	=	$this->cacheRoot;
						$this->buildCacheDir($directoryExp, $resetCachRoot,'server_rw');
						# open the cache file for writing
						$fp = @fopen($this->cachefile, 'w');
						# save the contents of output buffer to the file
						$this->data	=	ob_get_contents();
						# Send the output to the browser
						ob_end_clean();
						# Write to cache file
						if($fp) {
							fwrite($fp,$this->data);
							 # close the file
							fclose($fp);
						}
						else {
							$msg	=	"Cache failed".PHP_EOL."Likely Permissions".PHP_EOL;
							$fName	=	array(
											'path'=>NBR_CLIENT_DIR.DS."settings".DS.'reporting'.DS."errorlogs".DS,
											'filename'=>"cache.log.txt"
										);
							$opts	=	array(
											"skip_post"=>true
										);
							# Write to log if there is a fail
							$this->getHelper('nLogger')->saveToLog($fName,$msg,array('logging','exceptions'),$opts);
						}
					}
				}
				
				$this->data	=	(!empty($this->data))? $this->data : false;
				return $this;
			}
			
		public	function checkCacheFile($filename = false)
			{	
				$this->compile	=	true;
				$this->filename	=	$filename;
				if($this->isDir(pathinfo($this->filename,PATHINFO_DIRNAME)) && is_file($this->filename)) {
					$this->compile	=	false;
				}
				
				return $this;
			}
			
		public	function startCaching()
			{
				if(!$this->allowRender())
					return $this;

				ob_start();
				return $this;
			}
		
		public	function endCaching($file = false)
			{
				if(!$this->allowRender())
					return $this;
					
				$this->data	=	ob_get_contents();
				ob_end_clean();
				
				return $this;
			}
		
		public	function getCached()
			{
				if(!empty($this->data))
					return $this->data;
			}
		
		public	function addContent($file = false)
			{
				if(!$this->allowRender())
					return false;
				
				$this->content	=	$file;
				$this->saveFile($file,$this->filename);
				
				return $this;
			}
		
		public	function renderBlock($type = 'string')
			{
				ob_start();
				
				if(!$this->allowRender())
					include($this->filename);
				else {
					if($type != 'string' && is_file($this->content))
						include($this->content);
					else
						echo (is_string($this->content))? $this->content : "";
				}
				
				$data	=	ob_get_contents();
				ob_end_clean();
			
				return $data;
			}
		
		public	function allowRender()
			{
				return $this->compile;
			}
	}