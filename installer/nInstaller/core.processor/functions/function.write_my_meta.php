<?php
	function write_my_meta($filename = false)
		{
			include_once(__DIR__."/function.version_from_file.php");
			$version	=	version_from_file($filename);
			if(!$version)
				return false;
			
			AutoloadFunction("get_file_extension");
			
			$ext		=	get_file_extension($filename);
			$filename	=	str_replace(ROOT_DIR,"",$filename);
			switch($ext) {
					case('js'):
						return '<script type="text/javascript" src="'.$filename.$version.'"></script>'.PHP_EOL;
					case('css'):
						return '<link type="text/css" rel="stylesheet" href="'.$filename.$version.'" />'.PHP_EOL;
				}
		}
?>