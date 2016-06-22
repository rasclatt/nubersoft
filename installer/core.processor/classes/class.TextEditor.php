<?php
	class	TextEditor
		{
			public	function __construct()
				{
					if(!function_exists("markup_temp")) {
						function markup_temp($matches)
							{
								$matches[0]	=	Safe::decode($matches[0]);
								
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
							
					$this->ProcessFile();
				}
			
			public	function Form($filename = false)
				{
					AutoloadFunction('read_from_file,get_file_description,check_empty');
					$filesize	=	(is_file($filename))? filesize($filename):0;
					$viewing	=	basename($filename);
					$file_raw	=	str_replace(NBR_ROOT_DIR,"",$filename);
					
					if(is_file($filename))
						$data		=	read_from_file($filename);
					
					$file_info	=	get_file_description($filename,15);
					$filename	=	Safe::encode(base64_encode($filename));
					
					include(NBR_RENDER_LIB."/class.html/TextEditor/Form.php");
				}
			
			public	function ProcessFile()
				{
					if(isset($_POST['save_file'])) {
						AutoloadFunction('check_empty');
						if(is_file($filemod = base64_decode(Safe::decode(urldecode($_POST['filename']))))) {
							if(check_empty($_POST,'delete','on')) {
								if(is_file($filemod)) {
									unlink($filemod);
									global $_error;
									$_error['file']	=	'File deleted.';
									return;
								}
							}
								 
							// Delete file if exists
							unlink($filemod);
							// New instance of file-writer
							$WriteFile			=	new WriteToFile();							
							$_POST['content']	=	Safe::decode($_POST['content']);
							$array				=	array("save_to"=>$filemod,"content"=>$_POST['content']);
							$success			=	$WriteFile->AddInput($array)->SaveDocument();
						}
					}
				}
			
			public	function FileList($directory = false)
				{
					AutoloadFunction('get_directory_list,get_files_folders');	
					$directory	=	($directory != false && !empty($directory))? $directory:NBR_CLIENT_DIR."/";
					$dir		=	get_files_folders($directory);
					// Create thumbnail previewer
					$filecheck	=	new FileMaster();
					$img_file	=	$filecheck->Initialize();
					
					include(NBR_RENDER_LIB.'/class.html/TextEditor/FileList.php');
					
				}
			
			protected	function DownloadFile($add_file = false, $filename = false, $root = false, $download = false)
				{
					if($add_file == false || empty($filename))
						return;
						
					$filename	=	(!$filename || empty($filename))? basename($add_file):$filename;
					$root		=	(!$root || empty($root))? NBR_ROOT_DIR.'/temp':$root;
					
					$Zipper		=	new ZipEngine(NBR_ROOT_DIR);
					$Zipper->AddFile($add_file)->Zipit($filename,$download);
				}
		}