<?php
	class	Updater implements UpdateSys
		{
			public		$errors;
			public		$final_report;
			public		$resonse;
			
			protected	static	$Zipper;
			protected	static	$RecursiveDelete;
			
			protected	$destination;
			protected	$filename;
			protected	$zipname;
			protected	$localTemp;
			
			public	function __construct()
				{ 
				}
				
			public	function setDestination($destination = false)
				{
					$this->destination	=	$destination;
					
					if(empty($this->destination))
						return $this;

					if(!is_dir($this->destination))
						mkdir($this->destination,0755,true);
					
					return $this;
				}
			
			public	function getFiles($zipname = false,$tempFolder = 'temp/')
				{
					// .zip file
					$this->zipname		=	$zipname;
					// temp directory where extract to
					$this->localTemp	=	false;
					// Check if empty
					if(empty($this->zipname) || !is_file($this->zipname))
						return $this;
					// Create zip engine
					self::$Zipper	=	new ZipEngine($this->zipname);
					// Assign the local temp folder
					$local	=	str_replace("//","/",$tempFolder);
					// Create the directory
					if(!is_dir($local))
						mkdir($local,0755,true);
					else {
							AutoloadFunction("FetchUniqueId");
							$local	=	mkdir(str_replace("//","/",rtrim($local,"/").FetchUniqueId()."/"),0755,true);
						}
					// Archive the temp folder
					$this->moveFrom($local);
					
					// Extract the zip file into the temp folder
					if(!empty($this->localTemp))
						self::$Zipper->UnZipit($this->zipname, $this->localTemp);
					
					return $this;
				}
			
			public	function moveFrom($moveFrom = false)
				{
					$this->localTemp	=	(is_dir($moveFrom))? $moveFrom : false;
						
					return $this;
				}
			
			public	function moveFiles($supprErrs = true)
				{
					if(empty($this->localTemp) || empty($this->destination))
						return $this;
						
					$install_from	=	$this->localTemp;
					$install_into	=	$this->destination;
					$getRoot		=	scandir($install_from);
					foreach($getRoot as $subdir) {
							$iFile = $install_from."/".$subdir;
							if($subdir != '.' && $subdir != '..' && is_dir($iFile)) {
									$install_from	= $iFile;
									break;
								}
						}
					
					$install_from	=	str_replace("//","/",$install_from);
					
					// Set the real path for the final unzip location
					$path		=	$this->destination;														
					$iterator	=	new RecursiveDirectoryIterator($install_from);
					$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
					$filter		=	new MyRecursiveFilterIterator($iterator);
					$objects	=	new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);
					$set[]		=	$install_from;
					$set[]		=	$this->destination;
					
					// Loop through files and folders to replace existing files and folders.
					foreach($objects as $name => $object) {
							// Copy to file/folder destination
							$root_dir	=	str_replace("//","/",str_replace($install_from, $install_into."/", $name));
							if(is_dir($name)) {
									if(!is_dir($root_dir)) {
											if(!mkdir($root_dir, 0775)) {
													if(!$supprErrs)
														$this->final_report['error']['dir'][]	=	str_replace($install_into, "", $root_dir);
													$process_failed	=	true;
													break;
												}
											else {
													if(!$supprErrs)
														$this->final_report['success']['dir'][]	=	str_replace($install_into, "", $root_dir);
												}
										}
									else {
											if(!$supprErrs)
												$this->final_report['skip']['dir'][]	=	str_replace($install_into, "", $root_dir);
										}
								}
							elseif(is_file($name)) {
									if(is_file($root_dir)) {
											if(filemtime($root_dir) >= filemtime($name)) {
													if(!$supprErrs)
														$this->final_report['skip']['copy'][]	=	str_replace($install_into, "", $root_dir);
													
													continue;
												}
										}
										
									if(@copy($name, $root_dir)) {
											if(!$supprErrs) {
													$this->final_report['copy']['from'][]	=	str_replace($install_into, "", $name);
													$this->final_report['copy']['to'][]		=	str_replace($install_into, "", $root_dir);
												}
										}
								}
						}
					
					
					if($supprErrs)
						$this->final_report['error']['general']	=	'Errors Supressed!';
						
					// Set defaults for reporting
					$this->final_report['error']['dir']		=	(!isset($this->final_report['error']['dir']))? 0:$this->final_report['error']['dir'];
					$this->final_report['success']['dir']	=	(!isset($this->final_report['success']['dir']))? 0:$this->final_report['success']['dir'];
					$this->final_report['skip']['dir']		=	(!isset($this->final_report['skip']['dir']))? 0:$this->final_report['skip']['dir'];
					$this->final_report['copy']['from']		=	(!isset($this->final_report['copy']['from']))? 0:$this->final_report['copy']['from'];
					$this->final_report['copy']['to']		=	(!isset($this->final_report['copy']['to']))? 0:$this->final_report['copy']['to'];
					
					return $this;
				}
			
			public	function getReport()
				{
					if(!empty($this->final_report))
						return $this->final_report;
				}
			
			public	function deleteFiles()
				{
					if(!empty($this->localTemp) && is_dir($this->localTemp)) {
							self::$RecursiveDelete	=	new recursiveDelete();
							self::$RecursiveDelete->delete($this->localTemp);
						}
					
					if(!empty($this->zipname) && is_file($this->zipname))
						unlink($this->zipname);
					
					return $this;
				}
			
			public	function addToMove($settings = false)
				{
					if(empty($settings) || !is_array($settings))
						return $this;
					
					foreach($settings as $key => $value) {
							if(is_file($value['from'])) {
									$name	=	basename($value['to']);
									$dir	=	str_replace($name,"",$value['to']);
									
									if(!is_dir($dir))
										mkdir($dir,0755,1);
									
									if(@copy($value['from'],$value['to']))
										chmod($value['to'],0644);
								}
							elseif(is_dir($value['from'])) {
									$this->copyToDest($this->recurseCopy($value['from']));
								}
						}
				}
			
			public	function InstallPackage($to, $from = 'http://www.nubersoft.com/client_assets/builds',$temp = '/client_assets/updates')
				{
					$this->destination	=	$to;
					$mothership			=	'http://www.nubersoft.com';
					$url				=	$mothership.'/api/index.php?service=Fetch.Update';
					$cURL				=	new cURL($url);
					$returned			=	$cURL->response;
					$pkg['version']		=	str_replace(".zip","",preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{1,})/',"$1-$2-$3 $4:$5:$6",$returned['version']));
					$pkg['url']			=	$mothership.$returned['url'];
					$pkg['size']		=	$returned['size'];
					
					$this->response		=	$pkg;
					// Remote file
					$installer			=	file_get_contents($pkg['url']);
					// Current temp download folder
					$local_space		=	$this->destination;
					// Temp folder for saving zip file
					$temp_folder		=	$local_space.$temp;
					// Extracted folder for zipped files
					$extracted			=	$temp_folder.'/extracted';
					
					// If temp file not exists, make it
					if(!is_dir($temp_folder))
						mkdir($temp_folder,0755,1);	
					// Try to install again
					if(!is_dir($temp_folder))
						return $this;
					$destination		=	$temp_folder.$returned['version'];
					// Remove current if exists
					if(is_file($destination)) {
							chmod($destination,0777);
							unlink($destination);
						}
					// Move file from remote to local temp folder
					file_put_contents($destination,$installer);
					// If file is available to extract, keep on
					if(!is_file($destination))
						return $this;
					// Start up extract engine 
					$Zipper	=	new ZipEngine($local_space);
					// Try to move and unzip
					if(!is_dir($extracted))
						mkdir($extracted,0755,1);
					// Extract to extact folder
					$Zipper->UnZipit($destination,$extracted);
					// Remove zipped file
					if(is_file($destination))
						unlink($destination);
					// Copy all files over to main root
					$this->MoveAndBackup($extracted,$local_space);
					// Remove temp files
					if(is_dir($temp_folder))
						recursiveDelete::delete($temp_folder);
					
					return $this;
				}
				
			public	function MoveAndBackup($install_from = false, $install_into = false, $backuplocation = false)
				{
					$install_from	=	str_replace("//","/",$install_from);
					
					if($install_from == false)
						return $this;
						
					// Set the real path for the final unzip location
					$path		=	$install_into;														
					$iterator	=	new RecursiveDirectoryIterator($path);
					$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
					$filter		=	new MyRecursiveFilterIterator($iterator);
					$objects	=	new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);
					// Loop through files and folders to replace existing files and folders.
					foreach($objects as $name => $object) {
							// Copy to file/folder destination
							$root_dir	=	str_replace("//","/",str_replace($install_from, $install_into, $name));
							if(is_dir($name)) {
									if(!is_dir($root_dir)) {
											if(!mkdir($root_dir, 0775)) {
													$this->final_report['error']['dir'][]	=	str_replace($install_into, "", $root_dir);
													$process_failed	=	true;
													break;
												}
											else
												$this->final_report['success']['dir'][]	=	str_replace($install_into, "", $root_dir);
										}
									else 
										$this->final_report['skip']['dir'][]	=	str_replace($install_into, "", $root_dir);
								}
							elseif(is_file($name)) {
									if(copy($name, $root_dir)) {
											$this->final_report['copy']['from'][]	=	str_replace($install_into, "", $name);
											$this->final_report['copy']['to'][]		=	str_replace($install_into, "", $root_dir);
										}
								}
						}
						
					// Set defaults for reporting
					$this->final_report['error']['dir']		=	(!isset($this->final_report['error']['dir']))? 0:$this->final_report['error']['dir'];
					$this->final_report['success']['dir']	=	(!isset($this->final_report['success']['dir']))? 0:$this->final_report['success']['dir'];
					$this->final_report['skip']['dir']		=	(!isset($this->final_report['skip']['dir']))? 0:$this->final_report['skip']['dir'];
					$this->final_report['copy']['from']		=	(!isset($this->final_report['copy']['from']))? 0:$this->final_report['copy']['from'];
					$this->final_report['copy']['to']		=	(!isset($this->final_report['copy']['to']))? 0:$this->final_report['copy']['to'];
					
					return $this;
				}
			
			protected	public	function recurseCopy($path = false)
				{
					if(empty($path))
						return false;
						
					$iterator	=	new RecursiveDirectoryIterator($path);
					$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
					$filter		=	new MyRecursiveFilterIterator($iterator);
					$objects	=	new RecursiveIteratorIterator($filter,RecursiveIteratorIterator::SELF_FIRST);
					
					return $objects;
				}
			
			protected	public	function copyToDest($objects = false)
				{
					if(empty($objects))
						return false;
						
					// Loop through files and folders to replace existing files and folders.
					foreach($objects as $name => $object) {
							// Copy to file/folder destination
							$root_dir	=	str_replace("//","/",str_replace($install_from, $install_into."/", $name));
							if(is_dir($name)) {
									if(!is_dir($root_dir)) {
											if(!mkdir($root_dir, 0775)) {
													if(!$supprErrs)
														$this->final_report['error']['dir'][]	=	str_replace($install_into, "", $root_dir);
													$process_failed	=	true;
													break;
												}
											else {
													if(!$supprErrs)
														$this->final_report['success']['dir'][]	=	str_replace($install_into, "", $root_dir);
												}
										}
									else {
											if(!$supprErrs)
												$this->final_report['skip']['dir'][]	=	str_replace($install_into, "", $root_dir);
										}
								}
							elseif(is_file($name)) {
									if(is_file($root_dir)) {
											if(filemtime($root_dir) >= filemtime($name)) {
													if(!$supprErrs)
														$this->final_report['skip']['copy'][]	=	str_replace($install_into, "", $root_dir);
													
													continue;
												}
										}
										
									if(@copy($name, $root_dir)) {
											if(!$supprErrs) {
													$this->final_report['copy']['from'][]	=	str_replace($install_into, "", $name);
													$this->final_report['copy']['to'][]		=	str_replace($install_into, "", $root_dir);
												}
										}
								}
						}
				}
		}