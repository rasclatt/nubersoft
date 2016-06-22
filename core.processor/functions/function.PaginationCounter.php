<?php
	function PaginationCounter($settings = false)
		{
			
			
			if(isset($settings['data']) && is_array($settings['data']))
				$SearchEngine	=	$settings['data'];
			else
				$SearchEngine	=	(!empty(\nApp::getDataNode('pagination')))? \nApp::getDataNode('pagination') : false;
				
			$searchbar		=	(isset($settings['layout']))? $settings['layout']:NBR_RENDER_LIB._DS_.'assets'._DS_.'app.search.pagination.php';
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