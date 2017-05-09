<?php
function rebuild_htaccess()
	{
		\Nubersoft\nApp::call()->autoload("create_htaccess_directive");
		$dirs	=	create_htaccess_directive();

		if(!empty($dirs['protect']) || !empty($dirs['unprotect'])) {
			$pro		=	array_filter($dirs['protect']);
			$unpro		=	array_filter($dirs['unprotect']);
			$unproplus	=	array_filter($dirs['unprotectset']);

			\Nubersoft\nApp::call()->autoload("create_htaccess");
			$looper	=	function($array,$instr = true,$script = false) {
					foreach($array as $dir) {
						$directory	=	str_replace("//","/",ROOT_DIR."/".$dir);
						if(is_file($htaccess = $directory.".htaccess")) {
							try {
									if(!@unlink($htaccess))
										throw new Exception("File could not be removed. Check permissions: ".str_replace(ROOT_DIR,"",$htaccess)." IN FILE: ".str_replace(ROOT_DIR,"",__FILE__)." ON LINE: ".__LINE__);
								}
							catch (Exception $e)
								{
									\Nubersoft\nApp::call()->saveIncidental("error.htaccess",$e->getMessage());
								}
						}
						
						if($instr) {
							if(!$script)
								create_htaccess(array("dir"=>$directory));
							else {
								create_htaccess(array_merge(array("dir"=>$directory),$script));
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