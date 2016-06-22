<?php
/*
** @description Removes .DS_Store files from the local OS X that may get pushed to the server accidentally
*/
function mac_ds_store_destroy($dir = false)
	{
		AutoloadFunction("get_directory_list,get_file_extension");
		$dir	=	(!empty($dir) && is_dir($dir))? $dir : NBR_ROOT_DIR;
		$dirs	=	get_directory_list(array("dir"=>$dir));
			
		foreach($dirs['host'] as $d) {
		if(get_file_extension($d) == 'DS_Store') {
			if(is_writable($d)) {
				if(!unlink($d))
					RegisteryEngine::saveIncidental("dsDestroy",false);
				}
			}
		}
	}