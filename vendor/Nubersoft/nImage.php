<?php
namespace Nubersoft;

class	nImage extends \Nubersoft\nApp
{
	public	function toBase64($string, $ext, $enc = false)
	{
		if(empty($string))
			return false;
		
		$b64	=	base64_encode($string);
		
		return ($enc)? $enc.$b64 : 'data:image/'.$ext.';base64,'.$b64;
	}
	
	public	function toBase64fromFile($file, $enc = false)
	{
		if(!is_file($file))
			return false;
		
		return $this->toBase64(file_get_contents($file), pathinfo($file, PATHINFO_EXTENSION), $enc);
	}
}