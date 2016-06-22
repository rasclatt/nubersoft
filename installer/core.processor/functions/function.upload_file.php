<?php
/*Title: upload_file()*/
/*Description: This function is the primary method of uploading files and sending file arrays back for recording to the database*/

	function upload_file($settings = array())
		{		
			register_use(__FUNCTION__);
			AutoloadFunction('check_empty,call_action,get_file_dir');
			$input_name	=	(!empty($settings['input_name']))? $settings['input_name']:'file';
			$table		=	(!empty($settings['table']))? $settings['table']:'image_bucket';
			$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_ROOT_DIR.get_file_dir($table);
			$thumb_dir	=	(!empty($settings['thumb_dir']))? $settings['thumb_dir']:NBR_CLIENT_DIR.'/thumbs/';
			$payload	=	(!empty($settings['payload']))? $settings['payload']:$_POST;
			$payload['requestTable']	=	$table;
			
			// Initialize a File Uploaders
			$files		=	new FileUploader(array("keyname"=>$input_name,"payload"=>$payload));
			// File counter
			$_fileCount	=	$files->fileCount();	
			// Check for files
			if($_fileCount > 0) {
				// Retrieve file name if file is being updated
				if(call_action('update')) {
					AutoloadFunction('nQuery');
					$nubquery	=	nQuery();
					$fetchFile	=	$nubquery	->select(array("file_name","file_path"))
												->from($table)
												->where(array("ID" => $payload['ID']))
												->fetch();

					$_fileName	=	(!empty($fetchFile[0]['file_name']))? $fetchFile[0]['file_name'] : false;
					
					if($_fileName) {
						AutoloadFunction('delete_upload');
						delete_upload(array("file_name"=>$fetchFile[0]['file_name'],"file_path"=>$fetchFile[0]['file_path']));
					}
				}
				else
					$_fileName	=	(check_empty($settings,'name',1))? 1 : false;
				
				
				// Return all the file values for set into table
				$uploads['files']	=	$files->prepare($_fileName)->execute($dir)->sql();
				$uploads['count']	=	$_fileCount;
				$uploads['bind']	=	$files->bind;
				
				return (!empty($uploads))? $uploads:false;
			}
				
			return false;
		}
?>