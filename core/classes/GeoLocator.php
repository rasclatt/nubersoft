<?php

class	GeoLocator
	{
		private	$website;
		private	$fullArr;
		
		public	function __construct($website = false,$json = false)
			{
				$this->fullArr	=	Safe::to_object(array());
				$this->website	=	(!empty($website))? $website : "http://www.geoplugin.net/php.gp";
				$cURL			=	new cURL();
				if(!$json)
					$this->fullArr	=	Safe::to_object(unserialize($cURL->Connect($this->website.'?ip='.$_SERVER['REMOTE_ADDR'],false)));
				else
					$this->fullArr	=	Safe::to_object($cURL->Connect($this->website));
			}
		
		public	function getAll()
			{
				return $this->fullArr;
			}
		
		public	function getAttr($key = false)
			{
				return (isset($this->fullArr->{$key}))? $this->fullArr->{$key} : false;
			}
	}