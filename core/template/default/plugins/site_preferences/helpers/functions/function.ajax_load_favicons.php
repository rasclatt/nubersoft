<?php
use \Nubersoft\nApp as nApp;

function ajax_load_favicons()
	{
		$nImage	=	nApp::call('nImage');
		$nApp	=	nApp::call();
		$nApp->autoload(array("get_directory_list"));
		$imgs	=	$nApp->getDirList(array("dir"=>NBR_ROOT_DIR,"type"=>array("ico","png"),"recursive"=>false));
		
		if(empty($imgs['root']))
			return "NO FAVICONS SET";
		
		foreach($imgs['root'] as $icns) {
			if(preg_match('/favicon\./',$icns))
				$fImg[]	=	$nImage->image($icns,array('style'=>"margin: 5px; height: 40px; width: 40px; border: 1px solid #FFF; box-shadow: 1px 1px 8px #000;"));
		}
		
		return (!empty($fImg))? implode("",$fImg) : '';
	}
	
header("Cache-control: max-age=0, must-revalidate");	
die(json_encode(array('html'=>array(ajax_load_favicons()),'sendto'=>array('#favIconList'))));