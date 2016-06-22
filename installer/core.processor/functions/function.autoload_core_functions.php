<?php
	function autoload_core_functions($load = 'config')
		{
			
			$processor	=	function($array) {
	
				foreach($array as $func => $path) {
						if(empty($path))
							$auto['autoload'][]	=	$func;
						else
							$auto[$path][]	=	$func;
					}
					
					
					if(!empty($auto)) {
						if(!empty($auto['autoload'])) {
							AutoloadFunction(implode(",",$auto['autoload']));
							unset($auto['autoload']);
						}
						
						if(!empty($auto)) {
							foreach($auto as $link => $func) {
								AutoloadFunction(implode(",",$func),$link);
							}
						}
					}
			};
			
			$prefs		=	NuberEngine::getRegFile();

			if($load == 'config' && !empty($prefs['onloadconfigfunctions'])) {
				$processor($prefs['onloadconfigfunctions']);
			}
			
			if($load == 'nuber' && !empty($prefs['onloadnuberfunctions'])) {
				$processor($prefs['onloadnuberfunctions']);
			}
		}