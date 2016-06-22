<?php
/*
**	@description	This function will rebuild an htaccess tree based on the reg file.
*/
function rebuild_htaccess()
	{
		AutoloadFunction("create_htaccess_directive");
		$dirs	=	create_htaccess_directive();

		if(!empty($dirs['protect']) || !empty($dirs['unprotect'])) {
			$pro		=	array_filter($dirs['protect']);
			$unpro		=	array_filter($dirs['unprotect']);
			$unproplus	=	array_filter($dirs['unprotectset']);
			$flush		=	$dirs['protectflush'];
			
			if($flush) {
				$nFunctions		=	new nUberSoft\nFunctions();
				$allHtaccess	=	$nFunctions->getDirList(array('dir'=>NBR_CLIENT_DIR,'type'=>array('htaccess'),'recursive'=>true));
				
				if(!empty($allHtaccess['host'])) {
					foreach($allHtaccess['host'] as $htaccess) {
						try {
							if(!@unlink($htaccess))
								throw new Exception("File could not be removed. Check permissions: ".str_replace(NBR_ROOT_DIR,"",$htaccess)." IN FILE: ".str_replace(NBR_ROOT_DIR,"",__FILE__)." ON LINE: ".__LINE__);
						}
						catch (Exception $e){
							nApp::saveIncidental("error.htaccess",$e->getMessage());
						}
					}
				}
			}
			
			AutoloadFunction("CreateHTACCESS");
			$looper	=	function($array,$instr = true,$script = false) {
					foreach($array as $dir) {
						$directory	=	str_replace("//","/",NBR_ROOT_DIR."/".$dir);
						if(is_file($htaccess = $directory.".htaccess")) {
							try {
									if(!@unlink($htaccess))
										throw new Exception("File could not be removed. Check permissions: ".str_replace(NBR_ROOT_DIR,"",$htaccess)." IN FILE: ".str_replace(NBR_ROOT_DIR,"",__FILE__)." ON LINE: ".__LINE__);
								}
							catch (Exception $e)
								{
									nApp::saveIncidental("error.htaccess",$e->getMessage());
								}
						}
						
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