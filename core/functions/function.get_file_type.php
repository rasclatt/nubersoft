<?php
function get_file_type($file = false,$resouce = false)
	{
		ini_set("max_execution_time",1000);
		if(strpos($file,".") !== false && $resouce != false) {
			$img			=	finfo_file($resouce,$file);
			$info			=	array_filter(explode("/",$img));
			$finfo['type']	=	(isset($info[0]))? $info[0]:"";
			$finfo['id']		=	(isset($info[1]))? $info[1]:"";
			return (object) $finfo;
		}
	}