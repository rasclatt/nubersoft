<?php
	function get_local_file($filename = false,$default = false)
		{
			register_use(__FUNCTION__);
			if($filename == false)
				return;
			
			$backtrace	=	debug_backtrace();
			$current	=	(isset($backtrace[0]['file']))? explode("/",$backtrace[0]['file']) : $default;
			if(is_array($current)) {
					$current	=	array_filter($current);
					array_pop($current);	
					$current	=	"/".implode("/",$current)."/";
				}
				
			unset($backtrace);
			$template	=	str_replace("//","/",$current.$filename);
			
			return (is_file($template))? $template:$filename;
		}
?>