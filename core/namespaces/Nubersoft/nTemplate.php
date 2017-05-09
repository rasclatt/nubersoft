<?php
namespace Nubersoft;

class nTemplate extends \Nubersoft\nApp
	{
		protected $siteValue;
		
		public	function getFrontEnd($file = false)
			{
				return $this->determinePlace('frontend',$file);
			}
			
		public	function getBackEnd($file = false)
			{
				return $this->determinePlace('admintools',$file);
			}
			
		public	function getTemplateFrom($type,$file = false)
			{
				return $this->determinePlace($type,$file);
			}
			
		public	function determinePlace($type,$file=false)
			{
				# Fetches all the template areas
				$templates	=	$this->toArray($this->getSite('templates'));
				$is_dir		=	(empty(trim($file)));
				$file		=	(!empty($file))? trim($file,DS) : false;
				
				foreach($templates as $spot) {
					$template	=	(!$is_dir)? $spot['dir'].DS.$type.DS.$file : $spot['dir'].DS.$type;
					$fileDir	=	$this->toSingleDs(NBR_ROOT_DIR.DS.$template);
					if($is_dir) {
						if(is_dir($fileDir))
							return str_replace(NBR_ROOT_DIR,'',$fileDir);
					}
					else {
						if(is_file($fileDir))
							return str_replace(NBR_ROOT_DIR,'',$fileDir);
					}
				}
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
	}