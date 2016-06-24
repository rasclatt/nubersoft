<?php
namespace Nubersoft;

class	nHtml
	{
		private	static	$singleton;
		private	$ROOT_DIR;
		
		public	function __construct()
			{
				$this->ROOT_DIR	=	 __DIR__._DS_.'nHtml'._DS_.'makeElement';
				if(!(self::$singleton instanceof \Nubersoft\nHtml))
					self::$singleton	=	$this;
					
				return self::$singleton;
			}
		/*
		**	@description	This method will create an html element using
		**	@param	$type [string]	This is the type of element to make
		**	@param	$attr	[array|boolean{false}|empty]	This will send the attributes to the include file
		**	@param	$template	[string]	This is a path to the template for the make element
		**	@param	$inc	[string]	This tells the render how to include the file
		*/
		public	function makeElement($type,$attr = false,$template = false,$inc = 'include')
			{
				$thisFunc	=	$this->ROOT_DIR._DS_.$type._DS_.'index.php';
				$find		=	(!empty($template) && is_file($template))? $template : $thisFunc;
				$inc		=	(!empty($attr['inc_type']))? $attr['inc_type'] : 'include';
				
				return \nApp::nFunc()->render($find,$inc,$attr);
			}
		
		public	function getMakeTypes()
			{
				$filter	=	array('.','..');
				$root	=	$this->ROOT_DIR;
				return array_diff($root,$filter);
			}
		
		public	function __call($name,$args = false)
			{
				$type		=	$name;
				$attr		=	(!empty($args[0]))? $args[0] : false;
				$template	=	(!empty($args[1]))? $args[1] : false;
				$inc		=	(!empty($args[2]))? $args[2] : 'include';
				
				return $this->makeElement($type,$attr,$template,$inc);
			}
	}