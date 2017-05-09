<?php
/*Title: buildDir()*/
/*Description: This function will build a series of folders.*/
/*ALERT: `This file is deprecated.`*/

function buildDir($directoryExp, $resetCachRoot,$htaccess = false)
	{
		
		$buildDirRoots	=	"";
		
		if(is_array($directoryExp)) {
				foreach($directoryExp as $keys => $values)
					{
						
						$buildDirRoots	.=	$values.DS;
						if(!is_dir($resetCachRoot))
							@mkdir($resetCachRoot, 0755,true);
							
						if(!is_dir($resetCachRoot . $buildDirRoots))
							@mkdir($resetCachRoot . $buildDirRoots, 0755,true);
					}
			}
		else
			@mkdir(str_replace(DS.DS,DS,$resetCachRoot.DS.$directoryExp), 0755,true);
		
		if($htaccess != false) {
				if(!is_file(str_replace(DS.DS,DS,$resetCachRoot.DS.".htaccess"))) {
						AutoloadFunction('CreateHTACCESS');
						CreateHTACCESS(array("rule"=>$htaccess,"dir"=>$resetCachRoot));
					}
			}
	}