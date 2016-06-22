<?php
	function version_from_file($filename = false)
		{
			if(is_file($filename))
				return "?v=".date("YmdHis",filemtime($filename));
		}