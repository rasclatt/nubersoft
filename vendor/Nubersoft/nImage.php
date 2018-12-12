<?php
namespace Nubersoft;

class	nImage extends \Nubersoft\Singleton
	{
		protected	$nHtml;
		
		public	function __construct()
			{
				$this->nHtml	=	new nHtml();
				
				return parent::__construct();
			}
		
		public	function toBase64($file,$encodeing = false)
			{
				if(!is_file($file)) {
					trigger_error('Base64 File is invalid',E_USER_NOTICE);
					return false;
				}
				
				$img	=	file_get_contents($file);
				
				if(empty($img))
					return false;
				$b64	=	base64_encode($img);
				return (!empty($encodeing))? $encodeing.$b64 : 'data:image/'.pathinfo($file,PATHINFO_EXTENSION).';base64,'.$b64;
			}
		
		public	function image($path,$options = false,$version = false,$local = true)
			{
				$settings	=	$this->parseAttr($options);
				return $this->nHtml->renderSource('img',$path,$local,$version,$settings);
			}
		
		public	function imageBase64($path,$options = false)
			{
				
				$settings	=	$this->parseAttr($options);
				return $this->nHtml->renderSource('img',$this->toBase64($path),false,false,$settings);
			}
			
		public	function src($path,$options = false,$version = false,$local = true)
			{
				$settings	=	$this->parseAttr($options);
				return $this->nHtml->renderSource('src',$path,$local,$version,$settings);
			}
		
		protected	function parseAttr($options)
			{
				$settings	=	array();
				if(is_array($options)) {
					foreach($options as $attr => $value) {
						$settings[]	=	"{$attr}='".$value."'";
					}
				}
					
				return $settings;
			}
	}