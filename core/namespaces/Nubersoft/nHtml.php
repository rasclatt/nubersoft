<?php
namespace Nubersoft;

class	nHtml extends \Nubersoft\Singleton
	{
		private	function getThisRootDir()
			{
				return __DIR__.DS.'nHtml'.DS.'makeElement';
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
				$thisFunc	=	$this->getThisRootDir().DS.$type.DS.'index.php';
				$find		=	(!empty($template) && is_file($template))? $template : $thisFunc;
				$inc		=	(!empty($attr['inc_type']))? $attr['inc_type'] : 'include';
				
				return nApp::call()->render($find,$inc,$attr);
			}
		
		public	function getMakeTypes()
			{
				$filter	=	array('.','..');
				$root	=	$this->getThisRootDir();
				return array_diff($root,$filter);
			}
		
		protected	function doSource($type = 'js', $fName = 'jsSource', $dir = false,$recursive = true,$useLocalUrl = true)
			{
				$dir		=	(!empty($dir))? $dir : NBR_CLIENT_DIR.'/'.$type.'/';
				$use		=	($recursive)? array("dir"=>$dir,"type"=>array($type)) : array("dir"=>$dir,"type"=>array($type),"recursive"=>false);
				$js			=	nApp::call()->getDirList($use);
				$localUrl	=	"";
				if($useLocalUrl) {
					$localUrl	=	nApp::call()->siteUrl();
				}
				if(empty($js['root']))
					return;
				$template	=	__DIR__.DS.'nHtml'.DS.$fName.DS.'index.php';
				$new		=	array();
				foreach($js['root'] as $key => $val) {
					$attr	=	array(
									'links'=>$js,
									'key'=>$key,
									'site_url'=>$localUrl,
									'path'=>str_replace(NBR_ROOT_DIR,'',$val),
									'longPath'=>$js['host'][$key]
								);
					
					$new[]	=	nApp::call()->render($template,'include',$attr);
				}
				
				$new	=	array_filter(array_unique($new));
				
				return (!empty($new))? implode(PHP_EOL,$new).PHP_EOL : '';
			}
		
		public	function jsSource($dir = false,$recursive = true,$useLocalUrl = true)
			{
				return $this->doSource('js',__FUNCTION__,$dir,$recursive,$useLocalUrl);
			}
			
		public	function cssSource($dir = false,$recursive = true,$useLocalUrl = true)
			{
				return $this->doSource('css',__FUNCTION__,$dir,$recursive,$useLocalUrl);
			}
		
		public	function styleSheet($path,$local = true,$version = true)
			{
				return $this->renderSource('stylesheet',$path,$local,$version);
			}
			
		public	function javaScript($path,$local = true,$version = true)
			{
				return $this->renderSource('javascript',$path,$local,$version);
			}
		
		public	function settingsToAttr($options)
			{
				$settings	=	array();
				if(is_array($options)) {
					foreach($options as $attr => $value) {
						$settings[]	=	"{$attr}='".$value."'";
					}
				}
				
				return (!empty($settings))? implode(' ',$settings) : false;
			}
		
		public	function a($path, $wrap, $settings = false, $local = true, $version = false)
			{
				return $this->renderSource('a',$path,$local,$version,$this->settingsToAttr($settings),$wrap);
			}
		
		public	function renderSource($type,$path,$local = true,$version = true,$settings = false,$wrap = false)
			{
				if($local && !is_file($path)) {
					trigger_error('Path is invalid:'.PHP_EOL.'['.$path.']',E_USER_NOTICE);
					return false;
				}
				
				$version	=	($version)? date("ymdhis",filemtime($path)) : false;
				$path		=	str_replace(DS,'/',nApp::call()->stripRoot($path));
				$url		=	($local)? nApp::call()->siteUrl().str_replace('//','/','/'.$path) : $path;
				ob_start();
				include(__DIR__.DS.'nHtml'.DS.'html'.DS."{$type}.php");
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data.PHP_EOL;
			}
		
		public	function __call($name,$args = false)
			{
				$type		=	$name;
				$attr		=	(!empty($args[0]))? $args[0] : false;
				$template	=	(!empty($args[1]))? $args[1] : false;
				$inc		=	(!empty($args[2]))? $args[2] : 'include';
				
				return $this->makeElement($type,$attr,$template,$inc);
			}
		/*
		**	@desription	Returns html from the /core/namespaces/Nubersoft/nHtml/html/ directory
		*/
		public	function getHtml($name,$content = false)
			{
				$inc = __DIR__.DS.'nHtml'.DS.'html'.DS.$name.".php";
				if(is_file($inc)) {
					ob_start();
					include($inc);
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
			}
		
		public	function processAttr($options)
			{
				if(!is_array($options))
					return '';
				
				foreach($options as $attr => $value) {
					$settings[]	=	"{$attr}='".$value."'";
				}
				
				return (!empty($settings))? ' '.implode(' ',$settings) : '';
			}
	}