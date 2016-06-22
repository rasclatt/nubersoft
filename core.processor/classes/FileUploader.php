<?php
	
	class	FileUploader
		{
			public		$bind;
			
			protected	$_keyname,
						$_keepName,
						$_fileArray,
						$ext,
						$_destination,
						$_sqlArray,
						$payload,
						$id,
						$change_required,
						$nFrom,
						$nTo,
						$mime;
			
			public	function __construct($array = false)
				{
					AutoloadFunction('get_file_extension');
					$this->change_required	=	false;
					$this->nFrom			=	false;
					$this->nTo				=	false;
					$this->payload			=	(isset($array['payload']))? $array['payload']: nApp::getPost();
					$this->_keyname			=	(isset($array['keyname']))? $array['keyname']: 'file';
				}
			
			public	function getFileInfo($file_path,$key = 'nFrom')
				{
					$mimer	=	new finfo();
					if(is_array($file_path)) {
						foreach($file_path as $path) {
							if(!is_file($path))
								continue;
								
							$this->mime[$key][]	=	$mimer->file($file_path);
						}
					}
					else {
						$this->mime[$key]	=	(!is_file($file_path))? false : $mimer->file($file_path);
					}
					
					return $this->mime;
				}
			
			public	function isSameName($in_database,$new)
				{
					$this->nFrom			=	pathinfo($in_database,PATHINFO_FILENAME);
					$this->nTo				=	pathinfo($new,PATHINFO_FILENAME);
					$this->change_required	=	($this->nFrom != $this->nTo);
					
					return $this->change_required;
				}

			public	function renameFile($curr_path,$file_name,$duplicate = false)
				{
					$mimeInfo	=	$this->getFileInfo($curr_path,'curr');
					$exp		=	explode(" ",$this->mime['curr']);
					$getMime	=	(!empty($exp[0]) && !empty($exp[1]))? strtolower(implode(_DS_,array(preg_replace('/[^a-zA-Z0-9]/','',$exp[1]),$exp[0]))) : false;
					$ext		=	pathinfo($curr_path,PATHINFO_EXTENSION);
					$file_path	=	pathinfo($curr_path,PATHINFO_DIRNAME);
					$file_name	=	preg_replace('![^'._DS_.'/a-zA-Z0-9\.\-\_]!','',pathinfo($file_name,PATHINFO_FILENAME));
					
					$oldName	=	$curr_path;
					$newName	=	str_replace(_DS_._DS_,_DS_,$file_path._DS_.$file_name.'.'.$ext);
					
					if(!is_file($oldName))
						return false;
					
					if($duplicate)
						return (copy($oldName,$newName))? $newName : $oldName;
					else
						return (rename($oldName,$newName))? $newName : $oldName;
				}
			
			public	function fileCount()
				{
					// Check valid files
					if(isset($_FILES[$this->_keyname]["error"])) {
						foreach($_FILES[$this->_keyname]["error"] as $key => $error) {
							$sum[]	=	($error == 0)? 1:0;
						}
					}
					
					// Count how many files are in the queue
					$count	=	(isset($sum))? array_sum($sum):0;
					return $count;
				}
				
			public	function prepare($_keepName = false)
				{
					// True means will retain upload name.
					// False means use date-based name
					// Value means save as that value
					$this->_keepName	=	$_keepName;
					
					// Count how many files are in the queue
					$count	=	count($_FILES[$this->_keyname]["error"]);
					
					// Loop through those files
					for($i = 0; $i < $count; $i++) {
						if($_FILES[$this->_keyname]["error"][$i] != 4) {
							$this->ext					=	get_file_extension($_FILES[$this->_keyname]["name"][$i]);
							$_fileSet[$i]['type']		=	$_FILES[$this->_keyname]["type"][$i];
							$_fileSet[$i]['temp_name']	=	$_FILES[$this->_keyname]["tmp_name"][$i];
							$_fileSet[$i]['size']		=	$_FILES[$this->_keyname]["size"][$i];
							$_fileSet[$i]['name']		=	preg_replace('!.[a-zA-Z0-9]{2,}$!',"",$_FILES[$this->_keyname]["name"][$i]);

							//die($this->renameFile($_fileSet[$i]['temp_name'],'test'));
							
							if($this->_keepName != false) {
								if($this->_keepName === true || $this->_keepName === 1)
									$_fileSet[$i]['name']		=	$this->naming($_fileSet[$i]['name']);
								elseif(strlen($this->_keepName) > 1)
									$_fileSet[$i]['name']		=	$this->naming(preg_replace('!.[a-zA-Z0-9]{2,}$!',"",$this->_keepName));
								else {
									AutoloadFunction("FetchUniqueId");
									$_fileSet[$i]['name']	=	$this->naming(FetchUniqueId(rand(1000,9999)));
								}
							}
							else {
								AutoloadFunction("FetchUniqueId");
								$_fileSet[$i]['name']	=	$this->naming(FetchUniqueId(rand(1000,9999)));
							}
								
							// Check to see if the stored file needs to be deleted
							$_compare	=	($this->_keepName !== $_fileSet[$i]['name'])? $this->_keepName:false;
							
							if($_compare !== false && isset($_name)) {
								$_fileSet[$i]['delete']	=	$_compare;
								// Unset the comparison incase it continues over to the next loop
								unset($_compare);
							}
							// Save extension of current file
							$_fileSet[$i]['ext']		=	$this->ext;
						}
					}
						
					// If array is valid, reset it so it starts at zero
					$this->_fileArray	=	(isset($_fileSet))? array_values($_fileSet):0;
					
					return $this;
				}
			
			protected	function naming($_fileName = '',  $_encrypt = false)
				{
					if(!function_exists("FetchUniqueId"))
						AutoloadFunction("FetchUniqueId");
						
					$_fileName	=	trim(preg_replace('/[^0-9a-zA-Z\-\_\.]/','',$_fileName));
					$_name		=	(empty($_fileName))? FetchUniqueId(rand(1000,9999)):$_fileName;

					if($_encrypt == true)
						$_name	=	base64_encode($_name);
					
					return $_name.".".strtolower($this->ext);
				}
			
			
			public	function execute($_basedir = false)
				{
					register_use(__METHOD__);
					AutoloadFunction('bind_array,check_empty');
					$_basedir	=	(!$_basedir)? NBR_ROOT_DIR.get_file_dir($this->payload['requestTable']):$_basedir;
					// Assign the destination folder for uploading files to
					$this->_destination	=	$_basedir;
					// If there are files to upload, lets do it!
					if($this->_fileArray !== 0) {
						AutoloadFunction('directory_exists');
						$_ValidDest	=	directory_exists($this->_destination,array('make'=>true));				
						// If folder exits
						if($_ValidDest == 1) {
							// If a thumnail request is made
							if(check_empty($this->payload,'thumbnail','1')) {
								// New instance
								$ImageFactory	=	new ImageFactory();
								$maxThumbSize	=	(defined('NBR_MAX_THUMB_SIZE'))? NBR_MAX_THUMB_SIZE : ImageFactory::SMALL_INPUT;
								$ImageFactory->SetFileSize($maxThumbSize);
								$useTable		=	(isset($this->payload['requestTable']))? $this->payload['requestTable'] : NubeData::$settings->engine->table_name;
								nApp::resetTableAttr($useTable);
								$thumbdir		=	(!defined('NBR_THUMB_DIR'))? NBR_ROOT_DIR._DS_.'client_assets'._DS_.'thumbs'._DS_:NBR_THUMB_DIR."/";
								$thumbdir		=	$thumbdir._DS_.$useTable._DS_;
								if(!is_dir($thumbdir))
									mkdir($thumbdir,0755,true);
							}
							$b	=	0;
							$fCount	=	count($this->_fileArray);
							for($i = 0; $i < $fCount; $i++) {
								if(isset($this->_fileArray[$i]['temp_name'])) {
									// If the file is moved to the propper spot, save sql data
									if(move_uploaded_file($this->_fileArray[$i]['temp_name'], $this->_destination.$this->_fileArray[$i]['name'])) {
										
											// If the thumnail has be set make sure it exists
											if(isset($ImageFactory) && is_dir($thumbdir)) {
												if(isset($this->payload['file_name'])) {
													$ImageFactory	->SearchLocation($thumbdir)
																	->SearchFor($thumbdir.$this->payload['file_name']);
												}
												
												$ImageFactory->Thumbnailer($this->_destination.$this->_fileArray[$i]['name'], 150,150,$thumbdir.$this->_fileArray[$i]['name']);
											}
											
											$this->_sqlArray[$i]['file_path']	=	str_replace(NBR_ROOT_DIR,"",$this->_destination);
											$this->_sqlArray[$i]['file_name']	=	$this->_fileArray[$i]['name'];
											$this->_sqlArray[$i]['file_size']	=	$this->_fileArray[$i]['size'];
											
											// If no match try and delete old file
											if(isset($this->_fileArray[$i]['delete'])) {
												if(is_file($_del = $this->_destination.$this->_fileArray[$i]['delete']))
													@unlink($_del);
											}
											// Create Bind values
											$this->bind['vals'][$i][':file_path'.$b]	=	str_replace(NBR_ROOT_DIR,"",$this->_destination);
											$this->bind['vals'][$i][':file_name'.$b]	=	$this->_fileArray[$i]['name'];
											$this->bind['vals'][$i][':file_size'.$b]	=	$this->_fileArray[$i]['size'];
											$this->bind['cols'][$i][]					=	':file_size'.$b;
											$this->bind['cols'][$i][]					=	':file_name'.$b;
											$this->bind['cols'][$i][]					=	':file_size'.$b;
											
											$i++;
										}
										else {
											global $_incidental;
											$_incidental['file_upload'][]	=	'Failed ('.$this->_fileArray[$i]['name'].')';
										}
									}

								$b++;
							}
						}
					}
						
					$this->_sqlArray =	(isset($this->_sqlArray))? $this->_sqlArray:false;
					
					return	$this;
				}
			
			
			public	function sql()
				{
					register_use(__METHOD__);
					// Send files and report back vitals
				//	$this->prepare($this->_keepName);
					// Send files and report back vitals
					$this->execute();
					
					$array	=	$this->_sqlArray;
					
					return $array;
				}
			
		}