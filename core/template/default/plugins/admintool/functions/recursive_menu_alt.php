<?php
use Nubersoft\nApp as nApp;

function recursive_menu_alt($array,$key)
	{
		if (!is_array($array))
			echo (!preg_match('/~NOLINK~/i',$key))? "\r\n".'<li><a href="'.$array.'">'.str_replace("_"," ",$key).'</a></li>' : "\r\n".'<li><div class="menu-no-link">'.$array.'</div></li>';
		elseif(!empty($key)) {
			echo (!preg_match('/~NOLINK~/',$key))? "\r\n".'<li><a href="#">'.str_replace("_"," ",$key).'</a></li>' : "\r\n".'<li><div class="menu-no-link">'.$key.'</div></li>';
		}
		
		if(is_array($array)) {
			echo "\r\n".'	<ul class="drop_menu_sub">';
			foreach($array as $subkey => $value)
				recursive_menu_alt($value,$subkey);
			echo "\r\n".'	</ul>';
		}
	}