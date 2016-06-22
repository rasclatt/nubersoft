<?php

	function recursive_menu($array,$key = false,$info = false) {
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
				
				
	if(!function_exists("recursive_menu_alt")) {
			function recursive_menu_alt($array,$key) {
					
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
		}
				
	function create_dropdown_nav($settings = false)
		{	
			
			AutoloadFunction("check_empty");
			$id			=	(!empty($settings['id']))?				$settings['id'] : "primary_nav_wrap";
			$array		=	(!empty($settings['struct']))?			$settings['struct'] : Safe::to_array(NubeData::$settings->menu_struc);
			$info		=	(!empty($settings['data']))?			$settings['data'] : Safe::to_array(NubeData::$settings->menu_data);
			$skip		=	(!empty($settings['no_menu']))?			$settings['no_menu'] : false;
			$add_pos	=	(!empty($settings['insert_where']))?	$settings['insert_where'] : false;
			
			

			// Check for manually added menus
			$additionals	=	"";
			if(isset($settings['insert']) && is_array($settings['insert'])) {
					ob_start();
					foreach($settings['insert'] as $key => $value) {
							echo '<li>'.PHP_EOL;
							if(!is_array($value)) {
									$name	=	str_replace("_"," ",$key);
									$path	=	$value;
									
									echo '<a href="'.$path.'">'.$name.'</a>';
								}
							else
								recursive_menu_alt($value,$key);
								
							echo PHP_EOL.'</li>'.PHP_EOL;
						}
						
					$additionals	=	ob_get_contents();
					ob_end_clean();
				}
				
			
			ob_start();
?>

	<nav id="primary_nav_wrap">
<?php
		// If manual added and the position is true
		// insert the menu before the default menu
	//	if($add_pos && !empty($additionals))
	//		echo $additionals;
		
		if(!$skip)
			echo recursive_menu(Safe::to_array(NubeData::$settings->menu_struc),false,Safe::to_array($info));
			
	//	if(!$add_pos && !empty($additionals))
	//		echo $additionals;
?>
	</nav>
<?php		$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>