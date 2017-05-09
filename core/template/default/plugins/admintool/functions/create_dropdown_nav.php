<?php
use Nubersoft\nApp as nApp;

function create_dropdown_nav($settings = false)
	{
		$id			=	(!empty($settings['id']))? $settings['id'] : "primary_nav_wrap";
		$array		=	(!empty($settings['struct']))? $settings['struct'] : nApp::call()->toArray(nApp::call()->getDataNode('menu_struc'));
		$info		=	(!empty($settings['data']))? $settings['data'] : nApp::call()->toArray(nApp::call()->getDataNode('menu_data'));
		$skip		=	(!empty($settings['no_menu']))? $settings['no_menu'] : false;
		$add_pos	=	(!empty($settings['insert_where']))? $settings['insert_where'] : false;
		
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
		echo recursive_menu(nApp::call()->toArray(nApp::call()->getDataNode('menu_struc')),false,nApp::call()->toArray($info));
		
//	if(!$add_pos && !empty($additionals))
//		echo $additionals;
?>
</nav>
<?php		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}