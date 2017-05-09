<?php
/*Title: upload_file()*/
/*Description: This function is the primary method of uploading files and sending file arrays back for recording to the database*/

	function upload_file($settings = array())
		{	
			AutoloadFunction('check_empty,call_action,get_file_dir');
			$input_name				=	(!empty($settings['input_name']))? $settings['input_name']:'file';
			$table					=	(!empty($settings['table']))? $settings['table']:'image_bucket';
			$dir					=	(!empty($settings['dir']))? $settings['dir'] : NBR_ROOT_DIR.get_file_dir($table);
			$thumb_dir				=	(!empty($settings['thumb_dir']))? $settings['thumb_dir']:NBR_CLIENT_DIR.'/thumbs/';
			$payload				=	(!empty($settings['payload']))? $settings['payload'] : nApp::getPost();
			$payload['requestTable']	=	$table;
			
			// Initialize a File Uploaders
			$files		=	new FileUploader(array("keyname"=>$input_name,"payload"=>$payload));
			// File counter
			$_fileCount	=	$files->fileCount();	
			// See if a file already exists in the database
			$nubquery	=	nQuery();
			$fetchFile	=	(!empty($payload['ID']))? $nubquery	->select(array("file_name","file_path"))
																->from($table)
																->where(array("ID" => $payload['ID']))
																->fetch() : 0;
			// Check for files
			if($_fileCount > 0) {
				// Retrieve file name if file is being updated
				if(call_action('update')) {
					// If file exists
					$_fileName	=	(!empty($fetchFile[0]['file_name']))? $fetchFile[0]['file_name'] : false;
					// Try and delete it
					if($_fileName) {
						AutoloadFunction('delete_upload');
						$isFile	=	delete_upload(array("file_name"=>$fetchFile[0]['file_name'],"file_path"=>$fetchFile[0]['file_path']));
					}
					
					if(!empty($payload['file_name'])) {
						$files->isSameName($payload['file_name'],$_fileName);
					}
				
				}
				else
					$_fileName	=	(check_empty($settings,'name',1))? 1 : false;
				
				
				// Return all the file values for set into table
				$uploads['files']	=	$files->prepare($_fileName)->execute($dir)->sql();
				$uploads['count']	=	$_fileCount;
				$uploads['bind']		=	$files->bind;
				
				return (!empty($uploads))? $uploads : false;
			}
			elseif($fetchFile != 0) {
				if(!empty($payload['file_name'])) {
					if($files->isSameName($fetchFile[0]['file_name'],$payload['file_name'])) {
						
						$dups		=	nQuery()	->select(array('file_name'))
												->from($table)
												->where(array('file_name'=>$fetchFile[0]['file_name']))
												->fetch();
						
						$copy		=	($dups != 0);
						$newFile	=	$files->renameFile(NBR_ROOT_DIR.$fetchFile[0]['file_path'].$fetchFile[0]['file_name'],$payload['file_name'],$copy);
						$filename	=	pathinfo($newFile,PATHINFO_BASENAME);
						
						if($filename) {
							$fSize						=	(!empty($fetchFile[0]['file_size']))? $fetchFile[0]['file_size'] : filesize($newFile);
							$uploads['files'][]			=	array('file_name'=>$filename,'file_path'=>$fetchFile[0]['file_path'],'file_size'=>$fSize);
							$uploads['count']			=	1;
							$uploads['bind']['vals']	[]	=	array(':file_name0'=>$filename,':file_path0'=>$fetchFile[0]['file_path'],':file_size0'=>$fSize);
							$uploads['bind']['cols']	[]	=	array(':file_name0',':file_path0',':file_size0');
						}
						else {
							$uploads['files'][]			=	array('file_name'=>'','file_path'=>'','file_size'=>'');
							$uploads['count']			=	0;
							$uploads['bind']['vals']	[]	=	array(':file_name0'=>'',':file_path0'=>'',':file_size0'=>'');
							$uploads['bind']['cols']	[]	=	array(':file_name0',':file_path0',':file_size0');
						}
						
						return $uploads;
					}
				}
			}
			
			return false;
		}