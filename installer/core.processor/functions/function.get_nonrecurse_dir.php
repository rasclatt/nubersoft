<?php
	function get_nonrecurse_dir($dir = false,$search)
		{
			if(is_dir($dir)) {
					$scanned	=	scandir($dir);
					foreach($scanned as $filename) {
							if(preg_match("/".$search."$/",$filename)) {
									$files['host'][]	=	str_replace("//","/",NBR_ROOT_DIR."/".str_replace(NBR_ROOT_DIR,"","{$dir}/{$filename}"));
									$files['root'][]	=	str_replace(NBR_ROOT_DIR,"",str_replace("//","/","{$dir}/{$filename}"));
								}
						}
				}
				
			return (!empty($files))? $files : false;
		}
?>