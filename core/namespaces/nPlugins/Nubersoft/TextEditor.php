<?php
namespace nPlugins\Nubersoft;

use \nPlugins\Nubersoft\CoreFileHandler\FileMaster as FileMaster;
use \Nubersoft\nApp as nApp;

class	TextEditor extends \Nubersoft\nRender
	{
		private	$nApp,
				$FileMaster,
				$nDownloader;
		
		public	function __construct()
			{
				# Create thumbnail previewer
				$this->FileMaster	=	new FileMaster();
				$this->nDownloader	=	$this->getHelper('nDownloader');
				
				if(!function_exists("markup_temp")) {
					function markup_temp($matches)
						{
							$matches[0]	=	nApp::call()->safe()->decode($matches[0]);
							
							return '<code class="te-markup">'.str_replace(array("if","else","{","}"),array('<span class="te-ie">if</span>','<span class="te-ie">else</span>','<span class="te-brackets">{</span>','<span class="te-brackets">}</span>'),preg_replace_callback('/[a-zA-Z0-9\_]{1,}\(|\"([^\"\<]{1,})\"|\'[^\'\<]{1,}\'|\$[a-zA-Z0-9\_]{1,}|function\s|\<[^\<\s]{1,}|[^\=][\>\?\/]{1,2}/',"markup_formatter", $matches[0])).'</code>';
						}
				}
					
				if(!function_exists("markup_formatter")) {
						
					function markup_formatter($matches)
						{
						//	printpre($matches);
							
							if(strpos($matches[0],"<") !== false)
								$format	=	'<span class="te-tag"><</span><span class="te-tag">'.str_replace("<","",$matches[0])."</span>";
							elseif(strpos($matches[0],'>') !== false)
								$format	=	'<span class="te-tag">'.$matches[0]."</span>";
							elseif(strpos($matches[0],"(") !== false && strpos($matches[0],'"') === false && strpos($matches[0],"'") === false) 
								$format	=	'<span class="te-arr-wrp">'.str_replace(array("array","("),array('<span class="te-arr">array</span>','<span class="te-par">(</span>'),$matches[0])."</span>";
							elseif(strpos($matches[0],'$') !== false)
								$format	=	'<span class="te-ie">'.$matches[0].'</span>';
							elseif(strpos($matches[0],"(") !== false && (strpos($matches[0],'"') !== false || strpos($matches[0],"'") !== false))
								$format	=	'<span class="te-quote">'.preg_replace_callback('/\"[^\"]{1,}\"|\'[^\']{1,}\'/',"markup_quotes", $matches[0])."</span>";
							elseif(strpos($matches[0],"'") !== false || strpos($matches[0],'"') !== false)
								$format	=	'<span class="te-str">'.$matches[0].'</span>';
							elseif(strpos($matches[0],"function") !== false)
								$format	=	'<span class="te-str">'.$matches[0].'</span>';
							else
								$format	=	$matches[0];
							
							$format	=	str_replace('define','<span class="te-str">define</span>',$format);
							
							return (isset($format))? $format:"";
						}
					}
				
				if(!function_exists("markup_quotes")) {
					function markup_quotes($matches)
						{
							$format	=	'<span style="color: #990000;">'.preg_replace('/(\'[^\']{1,}\')/','<span style="color:#888;">$1</span>',$matches[0])."</span>";
							return $format;
						}
				}
						
				$this->processFile();
				
				return parent::__construct();
			}
		
		public	function form($filename = false)
			{
				$filesize	=	(is_file($filename))? filesize($filename):0;
				$viewing	=	basename($filename);
				$file_raw	=	str_replace(NBR_ROOT_DIR,"",$filename);
				
				if(is_file($filename))
					$data		=	$this->FileMaster->readFromFile($filename);
				
				$file_info	=	$this->FileMaster->getFileDescription($filename,15);
				$filename	=	$this->safe()->encode(base64_encode($filename));
				
				include(__DIR__.DS.'TextEditor'.DS.'form.php');
			}
		
		public	function processFile()
			{
				if($this->getPost('save_file')) {
					if(is_file($filemod = base64_decode($this->safe()->decode(urldecode($this->getPost('filename')))))) {
						if($this->getPost('delete') == 'on') {
							if(is_file($filemod)) {
								unlink($filemod);
								$_error['file']	=	'File deleted.';
								return;
							}
						}
						# New instance of file-writer
						$array		=	array(
											"save_to"=>$filemod,
											"content"=>$this->safe()->decode($this->getPost('content')),
											'secure'=>false,
											'overwrite'=>true
										);
						
						$success	=	$this->writeToFile($array);
					}
				}
			}
		
		public	function fileList($directory = false)
			{
				$directory	=	($directory != false && !empty($directory))? $directory : NBR_CLIENT_DIR.DS;
				$dir		=	$this->getFilesFolders($directory);
				$img_file	=	$this->FileMaster->initialize($this);
				ob_start();
				include(__DIR__.DS.'TextEditor'.DS.'fileList.php');
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		protected	function downloadFile($add_file = false, $filename = false, $root = false, $download = false)
			{
				if($add_file == false || empty($filename))
					return;
					
				$filename	=	(!$filename || empty($filename))? basename($add_file):$filename;
				$root		=	(!$root || empty($root))? NBR_ROOT_DIR.DS.'temp':$root;
				
				$Zipper		=	new \ZipEngine(NBR_ROOT_DIR);
				$Zipper->AddFile($add_file)->Zipit($filename,$download);
			}
		
		protected	function getFilesFolders($directory = false,$settings = false)
			{
				if(empty($directory))
					return;
				
				$dirlist	=	$this->getDirList(array("dir"=>$directory));
				$name		=	$directory;
				$files		=	(isset($settings['files']) && is_array($settings['files']))? $settings['files']:array("php","css","js","txt","htm","jpg","jpeg","gif","pdf","zip","tif","pref","png");
				$encode		=	(isset($settings['enc']) && $settings['enc']);
				$strip		=	(isset($settings['strip']) && $settings['strip']);
				$preg		=	".".implode("|.",$files);
				
				if(!empty($dirlist['host'])) {
					if(empty($dirlist['host']))
						return;
				}
				else
					return;
					
				foreach($dirlist['host'] as $dirfile) {
					if(is_file($dirfile) || preg_match("/".$preg."$/",$dirfile)) {
						if(strpos($dirfile,'/.DS_Store') === false) {
							$name			=	dirname($dirfile);
							$new[$name][]	=	$dirfile;
						}
					}/*
					else {
							if(strpos($dirfile,'/.DS_Store') === false) {
									$name			=	str_replace(NBR_ROOT_DIR,"",$dirfile);
									if(!isset($new[$name]))
										$new[$name][]	=	str_replace(NBR_ROOT_DIR,"",$dirfile);
								}
						}
					*/
					if(isset($new[$name]) && is_array($new[$name]))
						$new[$name]	=	array_unique($new[$name]);
				}
				
				if(isset($new))
					return $new;
			}
	}