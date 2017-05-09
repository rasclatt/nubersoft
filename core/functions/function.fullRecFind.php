<?php

function fullRecFind($str,array $payload)
	{
		$output	=	false;
		// Explode on array bracket
		$match	=	explode("[",$str);
		// Return if not array
		if(!is_array($match))
			return false;
		// Revise array to remove array bracket
		foreach($match as $key => $value) {
			$match[$key]	=	str_replace(']','',$value);
		}
		// Fetch the array iterator search
		AutoloadFunction('get_key_value');
		// Loop through the array to find
		foreach($match as $key => $arKey) {
			$arr	=	Safe::to_array(get_key_value($payload,array($arKey),true));
			$arr	=	(isset($arr['data'][$arKey]))? $arr['data'][$arKey] : false;
			$output	=	(isset($arr[0]))? $arr[0] : false;
		}
		
		return $output;
	}