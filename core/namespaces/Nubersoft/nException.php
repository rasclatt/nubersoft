<?php
namespace Nubersoft;

class	nException extends \Exception
	{
		private	$opts,
				$extractArr;
		
		public	function saveToLog($filename)
			{
				$this->extractArr	=	(!empty($this->extractArr))? $this->extractArr : false;
				$this->opts			=	(!empty($this->opts))? $this->opts:false;
				
				nApp::call('nLogger')->saveToLog($filename,$this->getMessage(),$this->extractArr,$this->opts);
			}
		
		public	function setOptions($array)
			{
				$this->opts	=	$array;
				return $this;
			}
		
		public	function checkConfig($val)
			{
				$find				=	array('logging');
				$this->extractArr	=	(is_array($val))? array_merge($find,$val) : array_push($find,$val);
				return $this;
			}
	}