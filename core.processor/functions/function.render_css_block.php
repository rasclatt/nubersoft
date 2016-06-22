<?php

function render_css_block()
	{
		if(empty(nApp::getHeaderContent('css')))
			return false;
			
		ob_start();
		echo Safe::decode(nApp::getHeaderContent('css')).PHP_EOL;
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}