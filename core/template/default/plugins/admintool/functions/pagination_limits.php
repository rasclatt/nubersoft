<?php
use Nubersoft\nApp as nApp;

function pagination_limits($settings = false)
	{
		if(isset($settings['data']) && is_array($settings['data']))
			$SearchEngine	=	$settings['data'];
		else
			$SearchEngine	=	nApp::call()->getDataNode('pagination');
		
		$searchbar	=	nApp::call()->setKeyValue($settings,'layout',NBR_RENDER_LIB.DS.'assets'.DS.'app.search.max.php');
		$table_name	=	$SearchEngine->data->table;
		$maxlimits	=	(isset($settings['max_range']) && is_array($settings['max_range']))? $settings['max_range'] : array(5,10,20,50,100);
		
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