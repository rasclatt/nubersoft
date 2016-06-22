<?php
	function get_default_meta($meta = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('get_default_css,default_jQuery');
			if($meta != false && !empty($meta))
				return $meta;
			else
			$data	=	'
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="SHORTCUT ICON" HREF="/favicon.png">'.PHP_EOL.default_jQuery().PHP_EOL.get_default_css().PHP_EOL.'
<script src="/js/onthefly.js"></script>';
			
			return $data;
		}
?>