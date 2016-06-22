<?php

function render_js_block()
	{	
		if(empty(NubeData::$settings->preferences->header->content->javascript))
			return false;
		
		ob_start();
		echo Safe::decode(NubeData::$settings->preferences->header->content->javascript).PHP_EOL;
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}