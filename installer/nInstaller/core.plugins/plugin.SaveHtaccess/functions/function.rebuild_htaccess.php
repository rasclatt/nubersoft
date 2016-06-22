<?php
function rebuild_htaccess()
	{
		AutoloadFunction("create_htaccess_directive");
		$dirs	=	create_htaccess_directive();

		if(!empty($dirs['protect']) || !empty($dirs['unprotect'])) {
			$pro		=	array_filter($dirs['protect']);
			$unpro		=	array_filter($dirs['unprotect']);
			$unproplus	=	array_filter($dirs['unprotectset']);

			AutoloadFunction("CreateHTACCESS");
			$looper	=	function($array,$instr = true,$script = false) {
					foreach($array as $dir) {
						$directory	=	str_replace("//","/",ROOT_DIR."/".$dir);
						if(is_file($htaccess = $directory.".htaccess"))
							unlink($htaccess);
						
						if($instr) {
							if(!$script)
								CreateHTACCESS(array("dir"=>$directory));
							else {
								CreateHTACCESS(array_merge(array("dir"=>$directory),$script));
							}
						}
					}
				};

			if(!empty($pro))
				$looper($pro);

			if(!empty($unpro))
				$looper($unpro,false);
				
			if(!empty($unproplus))
				$looper($unproplus,true,array("script"=>"order allow,deny".PHP_EOL."allow from all"));
				
			RegistryEngine::saveIncidental('rebuild_htaccess',"run");
		}
	}