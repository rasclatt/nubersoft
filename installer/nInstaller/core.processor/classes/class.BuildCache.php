<?php
	class	BuildCache
		{
			public		$fileOutput;
			public		$cachefile;
			public		$_cache;
			public		$endCache;
			public		$cacheDir;
			public		$cacheRoot;
			public		$autoCache;
			
			protected	$allow;
			protected	$nuber;
			
			private		$data;
			
			public	function __construct()
				{	
					AutoloadFunction('compare,is_loggedin,is_admin');
				}
			
			public	function Initialize($fileOutput = 'html')
				{
					$this->_cache		=	false;
					// Checks if cache is set at all, if so make it on or false
					$this->autoCache	=	(compare(nApp::getCachedStatus(),'on'));
					// If user is allowed to view uncached (true)
					$this->allow		=	(!is_loggedin() || !is_admin());
					// If cache on
					if($this->autoCache) {
						// If not Admin or not logged in
						if($this->allow) {
							// Load is_loggedin
							AutoloadFunction('is_loggedin');
							$reqfilename		=	nApp::getPage("unique_id");
							$this->fileOutput	=	$fileOutput;
							$cacheroot			=	(!empty(nApp::getSite("cache_folder")))? nApp::getSite("cache_folder") : false;
							$this->cacheRoot	=	(!empty($cacheroot))? $cacheroot: ((defined("CACHE_DIR"))? CACHE_DIR : CLIENT_DIR.'/settings/cache/');
							// Set the directory found in the cache folder to either the usergroup name<br>
							//(numeric) or the id of the page (which will be the second var)
							$this->cacheDir		=	$this->cacheRoot.$reqfilename.'/';
							$this->cacheDir		.=	(is_loggedin())? nApp::getUser('usergroup'): 'root';
							$this->cacheDir		.=	(!empty($_SESSION['ID']))? "/".$_SESSION['ID']:"";
							// DocumentRoot/cache/$unique_id/$_SESSION/$unique_id.ext
							$this->cachefile	=	str_replace("//","/",$this->cacheDir.'/index.'.$this->fileOutput);
							// If there is a cache file, include it and return a true so that the flush happens
							// Let command know it needs caching
							$this->_cache		=	(is_file($this->cachefile));
						}
					}

					return $this;
				}

			public	function RenderDocument($string = false)
				{
					// If is a file, include saved file
					if($this->_cache) {
						ob_start();
						include($this->cachefile);
						$this->data	=	ob_get_contents();
						ob_end_clean();
						return $this;
					}
					
					$this->data	=	$string;
					
					// If is content not empty, run the cache
					if($this->data) {
						if((isset($this->_cache) && !$this->_cache) && ($this->allow)) {
							AutoloadFunction('buildDir');
							ob_start();
							echo $string;
							$directoryExp	=	str_replace($this->cacheRoot, "", $this->cacheDir);
							$resetCachRoot	=	$this->cacheRoot;
							buildDir($directoryExp, $resetCachRoot,'server_rw');
							// open the cache file for writing
							$fp = @fopen($this->cachefile, 'w');
							// save the contents of output buffer to the file
							$this->data	=	ob_get_contents();
							// Send the output to the browser
							ob_end_clean();
							// Write to cache file
							if($fp) {
								fwrite($fp,$this->data);
								 // close the file
								fclose($fp);
							}
							else {
								AutoloadFunction('QuickWrite');
								QuickWrite(array("data"=>"Cache failed".PHP_EOL."Likely Permissions".PHP_EOL,"dir"=>CLIENT_DIR."/settings/error_log/","filename"=>"cache.log.txt","skip_post"=>true,"mode"=>"c+"));
							}
						}
					}
					
					$this->data	=	(!empty($this->data))? $this->data : false;
					return $this;
				}
				
			public	function startCaching()
				{
					ob_start();
					return $this;
				}
			
			public	function getCached()
				{
					if(!empty($this->data))
						return $this->data;
				}
			
			public	function addContent($file = false)
				{
					if(is_file($file))
						include($file);
					else
						echo (is_string($file))? $file : "";
					
					return $this;
				}
			
			public	function endCaching($file = false)
				{
					$this->data	=	ob_get_contents();
					ob_end_clean();
					
					return $this;
				}
		}