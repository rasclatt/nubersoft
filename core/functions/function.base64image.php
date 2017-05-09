<?php
function base64image($file)
	{
		if(!is_file($file))
			return false;
		
		$img	=	file_get_contents($file);
		
		if(empty($img))
			return false;

		return 'data:image/'.pathinfo($file,PATHINFO_EXTENSION).';base64,'.base64_encode($img);
	}