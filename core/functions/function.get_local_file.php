<?php
function get_local_file($filename = false,$default = false)
	{
		
		if($filename == false)
			return;
		
		$backtrace	=	debug_backtrace();
		$current	=	(isset($backtrace[0]['file']))? explode(DS,$backtrace[0]['file']) : $default;
		if(is_array($current)) {
				$current	=	array_filter($current);
				array_pop($current);	
				$current	=	DS.implode(DS,$current).DS;
			}
			
		unset($backtrace);
		$template	=	str_replace(DS.DS,DS,$current.$filename);
		
		return (is_file($template))? $template:$filename;
	}