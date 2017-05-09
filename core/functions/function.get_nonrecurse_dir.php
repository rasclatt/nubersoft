<?php
function get_nonrecurse_dir($dir = false,$search)
	{
		if(is_dir($dir)) {
			$scanned	=	scandir($dir);
			foreach($scanned as $filename) {
				if(preg_match("/".$search."$/",$filename)) {
					$files['host'][]	=	str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.str_replace(NBR_ROOT_DIR,"",$dir.DS.$filename));
					$files['root'][]	=	str_replace(NBR_ROOT_DIR,"",str_replace(DS.DS,DS,$dir.DS.$filename));
				}
			}
		}
			
		return (!empty($files))? $files : false;
	}