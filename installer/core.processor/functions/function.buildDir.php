<?php
/*Title: buildDir()*/
/*Description: This function will build a series of folders.*/
/*ALERT: `This file is deprecated.`*/

	function buildDir($directoryExp, $resetCachRoot,$htaccess = false)
		{
			register_use(__FUNCTION__);
			$buildDirRoots	=	"";
			
			if(is_array($directoryExp)) {
					foreach($directoryExp as $keys => $values)
						{
							
							$buildDirRoots	.=	"$values/";
							if(!is_dir($resetCachRoot))
								@mkdir($resetCachRoot, 0755,true);
								
							if(!is_dir($resetCachRoot . $buildDirRoots))
								@mkdir($resetCachRoot . $buildDirRoots, 0755,true);
						}
				}
			else
				@mkdir(str_replace("//","/",$resetCachRoot."/".$directoryExp), 0755,true);
			
			if($htaccess != false) {
					if(!is_file(str_replace("//","/",$resetCachRoot."/.htaccess"))) {
							AutoloadFunction('CreateHTACCESS');
							CreateHTACCESS(array("rule"=>$htaccess,"dir"=>$resetCachRoot));
						}
				}
		}
?>