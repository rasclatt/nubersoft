<?php
function get_local_file($filename = false,$default = false)
	{
		
		if($filename == false)
			return;
		
		$backtrace	=	debug_backtrace();
		$current	=	(isset($backtrace[0]['file']))? explode(_DS_,$backtrace[0]['file']) : $default;
		if(is_array($current)) {
				$current	=	array_filter($current);
				array_pop($current);	
				$current	=	_DS_.implode(_DS_,$current)._DS_;
			}
			
		unset($backtrace);
		$template	=	str_replace(_DS_._DS_,_DS_,$current.$filename);
		
		return (is_file($template))? $template:$filename;
	}