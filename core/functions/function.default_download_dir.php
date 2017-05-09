<?php
function default_download_dir($iDir = false)
	{
		$iDir	=	($iDir != false)? $iDir : NBR_CLIENT_DIR.DS.'images'.DS;
		
		if(!is_dir($iDir))
			return 0;
		
		$folders	=	scandir($iDir);

		if(!empty($folders)) {
			$filter[]	=	'.DS_Store';
			$filter[]	=	'..';
			$filter[]	=	'.';
			$folders	=	array_diff($folders,$filter);
			$store		=	array();
			foreach($folders as $fdirs) {
				$parentdir	=	str_replace(DS.DS,DS,$iDir.DS.$fdirs);
				if(is_dir($parentdir)) {
					$pDir	=	scandir($parentdir);
					$pDir	=	array_diff($pDir,$filter);
					foreach($pDir as $cDir) {
						$directory	=	str_replace(array(NBR_ROOT_DIR,DS.DS),array("",DS),$parentdir.DS);
						
						if(!in_array($directory,$store)) {
							$dlFiles[]	=	array('file_path'=>$directory,"terms_req"=>false);
						}
							
						$store[]	=	$directory;
					}
				}
			}
		}
					
		return (!empty($dlFiles))? $dlFiles:0;
	}