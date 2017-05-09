<?php
use Nubersoft\nApp as nApp;

function recursive_menu($array,$key = false,$info = false)
	{
		if(isset($info[$key])) {
			$arr	=	$info[$key];
			$name	=	$arr['menu_name'];
			$path	=	$arr['full_path'];
		}
		
		$usepath	=	(isset($path))? '<a href="'.$path.'">'.$name.'</a>':"";
		
		if(is_array($array)) {
			foreach($array as $subkey => $value) {
				$return[$subkey]	=	recursive_menu($value,$subkey,$info);
			}
		}
		else
			$return	=	$usepath;
			
		return (is_array($return))? PHP_EOL."\t".$usepath.'<ul class="drop_menu_sub">'.PHP_EOL."\t\t\t<li>".implode('</li>'.PHP_EOL."\t\t\t<li>",$return).'</li>'.PHP_EOL.'</ul>':$return;
	}