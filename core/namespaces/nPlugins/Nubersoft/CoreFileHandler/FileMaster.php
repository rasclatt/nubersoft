<?php
namespace nPlugins\Nubersoft\CoreFileHandler;

class FileMaster extends \nPlugins\Nubersoft\CoreFileHandler
	{
		protected	$filename,
					$resource,
					$nApp;
		public		$fileinfo;
		
		public	function initialize(\Nubersoft\nApp $nApp)
			{
				$this->nApp		=	$nApp;
				$this->resource	=	finfo_open(FILEINFO_MIME_TYPE);
				
				return $this;
			}
		
		public	function getInfo($filename = false)
			{
				if(isset($this->resource)) {
					$this->filename	=	($filename != false)? $filename : false;
					$this->fileinfo	=	$this->getFileType($filename,$this->resource);
				}
					
				return $this;
			}
			
		protected	function getFileType($file = false,$resouce = false)
			{
				ini_set("max_execution_time",1000);
				if(strpos($file,".") !== false && $resouce != false) {
					$img			=	finfo_file($resouce,$file);
					$info			=	array_filter(explode("/",$img));
					$finfo['type']	=	(isset($info[0]))? $info[0]:"";
					$finfo['id']		=	(isset($info[1]))? $info[1]:"";
					return $this->toObject($finfo);
				}
			}
		
		public	function thumbnailPreview()
			{
				if(isset($this->resource)) {
					if(!empty($this->filename)) {
						return	(object) $this->renderThumbPreviewer($this->filename,$this->resource);
					}
				}
					
				return false;
			}
		
		public	function renderThumbPreviewer($file = false, $resource = false,$thumb = 60)
			{
				if(!$file)
					return false;
					
				$thumbDir	=	NBR_CLIENT_DIR.DS.'images'.DS.'text_editor';
				if(!$this->isDir($thumbDir)) {
					trigger_error('Could not create this plugin thumb folder. Previewing unavailable.',E_USER_NOTICE);
					return;
				}
				
				if(!is_file($this->nApp->toSingleDs($thumbDir.DS.'.htaccess'))) {
					
					$this->nApp->getHelper('nReWriter')->createHtaccess(array(
						'rule'=>'server_rw',
						'save_to'=>$thumbDir,
						'write'=>true
					));
				}
				
				$icn	=	(is_file($thmb = $thumbDir.DS.basename($file)))? $thmb : $file;
				$icn	=	str_replace(NBR_ROOT_DIR,"",$icn);
				$info	=	false;
				
				if($resource) {
					$doctype	=	$this->getFileType($file,$resource);
					if((isset($doctype->type) && $doctype->type != 'image') || (!isset($doctype->type)))
						return false;
					
					$info	=	(function_exists("getimagesize"))? getimagesize($file) : false;
				}
				ob_start(); ?>
				<div style="margin: 5px;" class="transpattern">
					<div style="background-image: url('<?php echo $icn; ?>'); background-repeat: no-repeat; background-size: contain; border: 1px solid #888; height: <?php echo $thumb; ?>px; width: <?php echo $thumb; ?>px; background-position: center;"></div>
				</div><?php
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return array("layout"=>$data,"dimensions"=>$info);
			}
		
		public	function getFileDescription($filename = false,$lines = 10)
			{
				
				if(!is_file($filename))
					return;
				
				// Fetch the first 10 lines of the file.
				$file	=	$this->readFromFile($filename,$lines);
				// Search for notes
				preg_match_all('!/\*([^\*\:]{1,})\:([^\*]{1,})\*/!',$file,$matches);
				
				if(!empty($matches[2])) {
					$dup	=	false;
					$mCount	=	count($matches[1]);
					for($i = 0; $i < $mCount; $i++) {
						$key	=	str_replace(" ","_",strtolower($matches[1][$i]));
						$val	=	$matches[2][$i];
						
						if(!isset($array[$key]))
							$array[$key]	=	$val;
						else {
							$dup	=	true;
							$a		=	(!isset($a))? 1:$a+1;
							$array[$key."_$a"]	=	$val;
						}
					}
						
					if(isset($array)) {
						if($dup == true && (!isset($array[$key."_0"]) && isset($array[$key]))) {
							$array[$key."_0"]	=	$array[$key];
							unset($array[$key]);
						}
					}
						
					$matches	=	(isset($array))? $array:$matches;
					ksort($matches,SORT_REGULAR);
				}
				
				if(isset($matches[0])) {
					if(empty($matches[0]))
						$matches	=	false;
				}
		
				return (!empty($matches))? $matches:false;
			}
		
		public	function readFromFile($filename = false,$lines = false)
			{
				
				$fw	=	fopen($filename,"r");
				
				if($lines == false) {
					$size	=	filesize($filename);
					$data	=	($size > 0)? trim(fread($fw,$size)):"";
				}
				else {
					$i = 0;
					ob_start();
					
					while(!feof($fw)) {
						echo fgets($fw);
						
						if($lines == $i)
							break;
						
						$i++;
					}
						
					$data	=	ob_get_contents();
					ob_end_clean();
					
				}
				
				fclose($fw);
				return	$data;
			}
		
		public	function getFileTypes($from = false)
			{
				if($from) {
					$reg	=	$this->getRegistry();
					
					if(isset($reg['allowfiles']))
						return array_values($reg['allowfiles']);
				}
				
				$files	=	$this->nApp->nQuery()
								->select(array("file_extension","file_type",'editable','readable'))
								->from("file_types")
								->where(array("page_live"=>"on"))
								->fetch();
				
				if($files == 0)
					return false;
				
				return (!empty($files))? $this->organizeByKey($files,'file_type',array('multi'=>true)) : false;
			}
	}