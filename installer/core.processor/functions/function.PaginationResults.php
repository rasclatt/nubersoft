<?php
	function PaginationResults($settings = false)
		{
			if(isset($settings['data']) && is_array($settings['data']))
				$SearchEngine	=	$settings['data'];
			else
				$SearchEngine	=	(isset(NubeData::$settings->pagination))? NubeData::$settings->pagination:false;

			$searchbar	=	(isset($settings['layout']))? NBR_ROOT_DIR.$settings['layout']:NBR_RENDER_LIB.'/assets/search.results.php';

			ob_start();
			if(is_file($searchbar)) {
				include($searchbar);
			}
			else
				echo "<!-- SEARCH RESULTS NOT FOUND! -->";
			$data	=	ob_get_contents();
			ob_end_clean();
			
			if(!empty($settings['write']))
				echo $data;
			else
				return	$data;
		}