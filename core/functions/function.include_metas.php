<?php
	function include_metas($global = false)
		{
			
			
			if(isset(nApp::getDataNode('meta')))
				return Safe::to_array(nApp::getDataNode('meta'));
			
			AutoloadFunction('get_directory_list,backtrace_file');
			$css	=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/css/'));
			$js		=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/js/'));
			$local	=	backtrace_file();
			
			if(!empty($local)) {
				$break	=	false;
				for($i = 0; $i < count($local); $i++) {
					foreach($local[$i] as $key => $value) {
						if(preg_match('/\/template/',$value)) {
							$local_files	=	get_directory_list(array("dir"=>str_replace(basename($value),"",$value),"type"=>array("css","js")));
							$break	=	true;
							break;
						}
					}
						
					if($break)
						break;
				}
			}
			
			$local			=	NULL;
			$local			=	$local_files;
			$local['list']	=	(isset($local['list']))? $local['list']:array();
			$css['list']	=	(isset($css['list']))? $css['list']:array();
			$js['list']		=	(isset($js['list']))? $js['list']:array();
			
			if(empty($css['list']) && empty($js['list']) && empty($local['list']))
				return false;
			
			$array			=	array();
			$includes		=	array_merge($js['list'],$css['list'],$local['list']);
			
			if(!empty($includes)) {
				$iCount	=	count($includes);
				for($i = 0; $i < $iCount; $i++) {
					$array['user_root'][]	=	$includes[$i];
					$array['user_local'][]	=	str_replace(NBR_ROOT_DIR,"",$includes[$i]);
				}
			}
				
			if($global) {
				nApp::saveSetting('meta',$array);
			}
			
			return (isset($array))? $array:false;
		}