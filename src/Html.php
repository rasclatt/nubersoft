<?php
namespace Nubersoft;

class Html extends nApp
{
	public	function createMeta($name, $content, $trunc = false)
	{
		return ($trunc)? '<meta '.$name.'="'.$content.'" />'.PHP_EOL : '<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
	}
	
	public	function createScript($src, $local = false, $type = false, $id = false, $attr = false)
	{
		if(empty($type))
			$type	=	'text/javascript';
		
		if(!empty($id))
			$id	=	' id="'.$id.'"';
		
		if($local)
			$src	.=	'?v='.filemtime(str_replace(DS.DS, DS, NBR_DOMAIN_CLIENT_DIR.DS.str_replace('/', DS, $src)));
		
		return '<script type="'.$type.'" src="'.$src.'"'.$id.' '.$attr.'></script>'.PHP_EOL;
	}
	
	public	function createLinkRel($src, $local = false, $type = false, $rel = false, $id = false)
	{
		if(empty($type))
			$type = 'text/css';
		
		if(empty($rel))
			$rel	= 'stylesheet';
		
		if(!empty($id))
			$id	=	' id="'.$id.'"';
		
		if($local)
			$src	.=	'?v='.filemtime(str_replace(DS.DS, DS, NBR_DOMAIN_CLIENT_DIR.DS.str_replace('/', DS, $src)));
		
		return '<link type="'.$type.'" rel="'.$rel.'" href="'.$src.'" />'.PHP_EOL;
	}
}