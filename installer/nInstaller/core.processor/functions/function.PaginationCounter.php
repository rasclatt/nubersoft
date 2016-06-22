<?php
	function PaginationCounter($settings = false)
		{
			register_use(__FUNCTION__);
			
			if(isset($settings['data']) && is_array($settings['data']))
				$SearchEngine	=	$settings['data'];
			else
				$SearchEngine	=	(isset(NubeData::$settings->pagination))? NubeData::$settings->pagination:false;
				
			$searchbar		=	(isset($settings['layout']))? $settings['layout']:RENDER_LIB.'/assets/app.search.pagination.php';
			$access			=	true;
			
			ob_start();
			if(is_file($searchbar))
				include($searchbar);
			else
				echo "<!-- SEARCH COUNTER NOT FOUND! -->";
			$data	=	ob_get_contents();
			ob_end_clean();
			
			if(isset($settings['write']) && $settings['write'] == true)
				echo $data;
			else
				return	$data;
		}