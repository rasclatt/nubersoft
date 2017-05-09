<?php
/*Title: delete_upload()*/
/*Description: This file will check if a file exists then delete it. It then checks if there is a thumbnail associated with the file and deletes it.*/
/*Example: 
`delete_upload('/client_assets/images/filename.jpg');`*/

function delete_upload($dir = array())
	{
		
		AutoloadFunction("check_empty");
		$file_path	=	(!empty($dir['file_path']))? $dir['file_path']:NBR_ROOT_DIR;
		$file_name	=	(!empty($dir['file_name']))? $dir['file_name']:false;	
		$full_path	=	(!empty($dir['full_path']))? $dir['full_path']:false;
		
		if($full_path != false) {
			if(is_file($full_path)) {
				if(unlink($full_path))
					$delete =  true;
			}
		}
		
		$thumb_dir	=	(!defined("NBR_THUMB_DIR"))? NBR_CLIENT_DIR."/thumbs/":NBR_THUMB_DIR;
		
		if(is_file($del_file = NBR_ROOT_DIR.$file_path.$file_name)) {
			if(unlink($del_file)) {
				$delete['file']	=	true;
				if(is_file($del_thumb = $thumb_dir.$file_name)) {
					if(unlink($del_thumb))
						$delete['thumb']	=	true;
				}
			}
		}
		
		return (isset($delete))? $delete : false;
	}