<?php
namespace nWordpress;

class ArrayWorks extends \Nubersoft\ArrayWorks
{
	public	function mapFields($map,$array)
	{
		foreach($map as $key => $value) {
			
			$mapped	=	preg_replace_callback('/~[^~]+~/',function($v) use($key, $array, &$map){
				$val	=	trim($v[0],'~');
				preg_match('/^(\[[^\]]+\])$/',$val,$match);
				
				if(!empty($match)) {
					$val	=	rtrim(ltrim($match[0],'['),']');
					$auto	=	\Nubersoft\nApp::call()->getPlugin('\nWordpress\Automator')->automate('~'.$val.'~', $array);
					
					if(is_array($auto)){
						$map	=	array_merge($auto,$map);
						return '';
					}
					else {
						return $auto;
					}
				}
				
			if(isset($array[$val]) && is_array($array[$val])) {
				if(!empty($array[$val]))
					$map	=	array_merge($array[$val],$map);
				
				return '';
			}
			else
				return (isset($array[$val]))? $array[$val] : '';
				
			},$value);
			
			
			$map[$key]	=	$mapped;
			
		}
		
		return array_filter($map);
	}
}