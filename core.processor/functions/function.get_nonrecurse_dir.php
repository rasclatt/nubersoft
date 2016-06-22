<?php
function get_nonrecurse_dir($dir = false,$search)
	{
		if(is_dir($dir)) {
			$scanned	=	scandir($dir);
			foreach($scanned as $filename) {
				if(preg_match("/".$search."$/",$filename)) {
					$files['host'][]	=	str_replace(_DS_._DS_,_DS_,NBR_ROOT_DIR._DS_.str_replace(NBR_ROOT_DIR,"",$dir._DS_.$filename));
					$files['root'][]	=	str_replace(NBR_ROOT_DIR,"",str_replace(_DS_._DS_,_DS_,$dir._DS_.$filename));
				}
			}
		}
			
		return (!empty($files))? $files : false;
	}