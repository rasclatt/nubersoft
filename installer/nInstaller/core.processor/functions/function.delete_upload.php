<?php
/*Title: delete_upload()*/
/*Description: This file will check if a file exists then delete it. It then checks if there is a thumbnail associated with the file and deletes it.*/
/*Example: 
`delete_upload('/client_assets/images/filename.jpg');`*/

	function delete_upload($dir = array())
		{
			register_use(__FUNCTION__);
			AutoloadFunction("check_empty");
			$file_path	=	(!empty($dir['file_path']))? $dir['file_path']:ROOT_DIR;
			$file_name	=	(!empty($dir['file_name']))? $dir['file_name']:false;	
			$full_path	=	(!empty($dir['full_path']))? $dir['full_path']:false;
			
			if($full_path != false) {
					if(is_file($full_path)) {
							if(unlink($full_path))
								$delete =  true;
						}
				}
			
			$thumb_dir	=	(!defined("THUMB_DIR"))? CLIENT_DIR."/thumbs/":THUMB_DIR;
			
			if(is_file($del_file = ROOT_DIR.$file_path.$file_name)) {
					if(unlink($del_file)) {
							if(is_file($del_thumb = $thumb_dir.$file_name))
								unlink($del_thumb);
						}
				}
			
			return (isset($delete))? true:false;
		}
?>