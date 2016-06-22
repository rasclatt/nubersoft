<?php
/*Title: get_files_folders()*/
/*Description: This function returns an array of folders and files.*/
	function get_files_folders($directory = false,$settings = false)
		{
			if(empty($directory))
				return;
			
			AutoloadFunction('get_directory_list');
			$dirlist	=	get_directory_list(array("dir"=>$directory));
			$name		=	$directory;
			$files		=	(isset($settings['files']) && is_array($settings['files']))? $settings['files']:array("php","css","js","txt","htm","jpg","jpeg","gif","pdf","zip","tif","pref","png");
			$encode		=	(isset($settings['enc']) && $settings['enc']);
			$strip		=	(isset($settings['strip']) && $settings['strip']);
			$preg		=	".".implode("|.",$files);
			
			if(!empty($dirlist['host'])) {
					if(empty($dirlist['host']))
						return;
				}
			else
				return;
				
			foreach($dirlist['host'] as $dirfile) {
					if(is_file($dirfile) || preg_match("/".$preg."$/",$dirfile)) {
							if(strpos($dirfile,'/.DS_Store') === false) {
									$name			=	dirname($dirfile);
									$new[$name][]	=	$dirfile;
								}
						}/*
					else {
							if(strpos($dirfile,'/.DS_Store') === false) {
									$name			=	str_replace(NBR_ROOT_DIR,"",$dirfile);
									if(!isset($new[$name]))
										$new[$name][]	=	str_replace(NBR_ROOT_DIR,"",$dirfile);
								}
						}
					*/
					if(isset($new[$name]) && is_array($new[$name]))
						$new[$name]	=	array_unique($new[$name]);
				}
						
			
			if(isset($new))
				return $new;
		}
?>