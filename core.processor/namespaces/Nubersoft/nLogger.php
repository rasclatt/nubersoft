<?php
namespace Nubersoft;

class nLogger
	{
		private	$attr,
				$path;
		private	static	$singleton;
		public	function __construct()
			{
				if(!(self::$singleton instanceof nLogger))
					self::$singleton	=	$this;
				
				return self::$singleton;
			}
		
		public	function setSavePath($path)
			{
				$this->path	=	$path;
				return $this;
			}
		
		public	function __call($name,$attr = false)
			{
				// Shorter sep name
				$ds		=	DIRECTORY_SEPARATOR;
				// Quickload the writer
				\nApp::nFunc()->autoload('QuickWrite',NBR_FUNCTIONS);
				// Set path
				$this->path	=	(empty($this->path))? NBR_CLIENT_DIR.$ds.'settings'.$ds.str_replace('_',$ds,strtolower($name)) : str_replace($ds.$ds,$ds,$this->path.$ds.str_replace('_',$ds,strtolower($name)));
				$this->attr	=	array(
								"data"=>$attr[0]['content'],
								"dir"=>rtrim($this->path,basename($this->path)),
								"filename"=>basename($this->path).'.log',
								'skip_post'=>true,
								'mode'=>'r+'
							);
				// Write the data
				QuickWrite($this->attr);
				if(!empty($attr[0]['headers']) && is_array($attr[0]['headers'])) {
					foreach($attr[0]['headers'] as $header)
						header($header);
				}
				
				if(!empty($attr[0]['die']))
					die($attr[0]['die']);
						
				return $this;
			}
	}