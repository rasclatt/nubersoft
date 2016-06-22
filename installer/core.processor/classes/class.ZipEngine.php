<?php
	class	ZipEngine
		{
			protected	$nubquery;
			protected	$files;
			protected	$zipname;
			protected	$headerAddr;
			protected	$rootdir;
			protected	$zipeng;
			
			public	function __construct($rootdir = '')
				{
					AutoloadFunction('nQuery');
					$this->nubquery	=	nQuery();
					$this->rootdir	=	$rootdir;
					// Create instance of engine
					$this->zipeng	=	new ZipArchive();

					if(!is_dir($this->rootdir) && $this->rootdir != false)
						@mkdir($this->rootdir,0755,true);
				}
			
			public	function FetchTable($table = false, $columns = array(), $filename = false)
				{
					if($table !== false && $filename !== false && !empty($columns)) {
							
							$filename	=	$this->rootdir.$filename.".csv";
							$output 	=	fopen($filename, 'w');
							fputcsv($output, $columns);
							
							$query		=	$this->nubquery->select($columns)->from($table)->fetch();
							
							if($query !== 0) {
									foreach($query as $rows) {
											fputcsv($output, $rows);
										}
								}
							
							$this->files[]	=	$filename;
							fclose($output);
						}
				}
				
			public	function Zipit($zipname = false)
				{
					if(isset($this->files) && !empty($this->files)) {
							
							ini_set("max_execution_time",3000);
							
							$this->zipname	=	($zipname == false)? date("YmdHis").uniqid().".zip":$zipname;
							$this->zipname	=	(stripos($this->zipname,".zip") === false)? $this->zipname.".zip":$this->zipname;
							$this->zipeng->open($this->rootdir.$this->zipname, ZipArchive::CREATE);
							
							foreach($this->files as $filelocation) {
									$this->zipeng->addFile($filelocation,basename($filelocation));
								//	unlink($filelocation);
								}
								
							$this->zipeng->close();
							
							$this->headerAddr	=	true;
							$this->DownloadZip();
						}
				}
			
			public	function ZipAndStore($zipname = false,$headerset = false)
				{
					if(isset($this->files) && !empty($this->files)) {
							$this->zipname	=	($zipname == false)? date("YmdHis").uniqid().".zip":$zipname;
							$this->zipeng->open($this->rootdir.$this->zipname, ZipArchive::CREATE);
							
							foreach($this->files as $filelocation) {
									$this->zipeng->addFile($filelocation,basename($filelocation));
								//	unlink($filelocation);
								}
								
							$this->headerAddr	=	$headerset;
						}
				}
			
			protected	function DownloadZip()
				{
					
					$file	=	$this->rootdir.$this->zipname;
					
					if(isset($this->headerAddr)) {
							
							$err404	=	(!is_file($file))? "404 File Not Found":false;
							$err403	=	(is_file($file) && !is_readable($file))? "403 Forbidden":false;
							
							if($err404 != false && $err403 != false)
								$use_err	=	($err404 != false)? $err404:$err403;
							
							if(isset($use_err)){
								header($_SERVER['SERVER_PROTOCOL'].$use_err);
								header("Status:".$use_err);
								echo str_replace(array("403 ","404 "),"",$use_err);
								exit;
							}
							
							header('Content-Description: File Transfer');
							header('Content-Type: application/zip');
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
							header("Content-Type: application/force-download");
							header("Content-Type: application/download");
							header("Content-Disposition: attachment;filename=".basename($file));
							header("Content-Transfer-Encoding: binary ");
							header('Content-Length: '.filesize($file));
							while(ob_get_level())
								ob_end_clean();
							flush();
							readfile($file);
							exit;
						}
				}
				
			public	function AddFiles($filename = false)
				{
					if($filename == false || empty($filename))
						return $this;
					
					if(is_file($filename))				
						$this->files[]	=	$filename;
					
					return $this;
				}
				
			public	function UnZipit($from = false, $to = false)
				{
					if($from == false || $to == false)
						return $this;
						
					$Zipper	=	$this->zipeng;
					$Zipper->open($from);
					$Zipper->extractTo($to);
					$Zipper->close();
				}
		}
?>