<?php
	function PaginationLimits($settings = false)
		{
			register_use(__FUNCTION__);
			if(isset($settings['data']) && is_array($settings['data']))
				$SearchEngine	=	$settings['data'];
			else
				$SearchEngine	=	(isset(NubeData::$settings->pagination))? NubeData::$settings->pagination:false;
			
			$searchbar			=	(isset($settings['layout']))? $settings['layout']:RENDER_LIB."/assets/app.search.max.php";
			$table_name			=	$SearchEngine->data->table;
			
			if(!empty($settings['max_range'])) {
					
					if(is_array($settings['max_range']))
						$maxlimits	=	$settings['max_range'];
					else {
							$split	=	explode(",",$settings['max_range']);
							$split	=	array_filter($split);
							
							if(!empty($split))
								$maxlimits	= $split;
						}
				}
		
			$maxlimits	=	(!isset($maxlimits))? array(5,10,20,50,100): $maxlimits;
			
			ob_start();
			if(is_file($searchbar))
				include($searchbar);
			else
				echo "<!-- SEARCH LIMITS NOT FOUND! -->";
			$data	=	ob_get_contents();
			ob_end_clean();
			
			if(isset($settings['write']) && $settings['write'])
				echo $data;
			else
				return	$data;
		}
?>