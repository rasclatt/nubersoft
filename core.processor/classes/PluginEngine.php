<?php
class	PluginEngine
	{
		private	$condMet = false;
		private	$dir;
		private	$pluginName;
		private	static	$singleton;
		
		public	function __construct($dir = false)
			{
				$this->dir	=	$dir;
				
				if(empty(self::$singleton))	
					self::$singleton	=	$this;
				
				return self::$singleton;
			}
		
		public	function initApp($pluginName = false)
			{
				$this->pluginName	=	$pluginName;
				
				if(is_file($plugin = str_replace(_DS_._DS_,_DS_,$this->dir._DS_.$this->pluginName._DS_."plugin.php")))
					include($plugin);
					
				return $this;
			}
		
		public	function appValid()
			{
				return (!empty($this->condMet));
			}
			
		public	function toPage()
			{
				return (!empty($this->condMet))? $this->condMet : "";
			}
	}