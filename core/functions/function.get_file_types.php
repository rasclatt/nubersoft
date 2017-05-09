<?php

function get_file_types($from = false)
	{
		if($from) {
			$reg	=	nApp::getRegistry();
			
			if(isset($reg['allowfiles']))
				return array_values($reg['allowfiles']);
		}
		
		$files	=	nQuery()	->select(array("file_extension","file_type"))
								->from("file_types")
								->where(array("page_live"=>"on"))
								->fetch();
		if($files == 0)
			return false;
		$fCount	=	count($files);
		for($i = 0; $i < $fCount; $i++) {
			$array[$files[$i]['file_type']][]	=	$files[$i]['file_extension'];
		}
		
		return (!empty($array))? $array : false;
	}