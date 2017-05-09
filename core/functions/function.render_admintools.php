<?php
function render_admintools($settings = false)
	{
		
		ob_start();
		include_once(NBR_RENDER_LIB.DS.'assets'.DS.'html'.DS.'admin.php');
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}