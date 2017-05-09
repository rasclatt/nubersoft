<?php
use Nubersoft\nApp as nApp;

function pagination_search_bar($settings = false)
	{
		if(isset($settings['data']) && is_array($settings['data']))
			$SearchEngine	=	$settings['data'];
		else
			$SearchEngine	=	nApp::call()->getDataNode('pagination');

		$searchbar	=	(isset($settings['layout']))? $settings['layout']:NBR_RENDER_LIB.DS.'assets'.DS.'app.search.form.php';
		$table_name	=	$SearchEngine->data->table;
		$submit		=	(isset($settings['submit']))? $settings['submit']:"Search";
		
		ob_start();
		if(is_file($searchbar))
			include($searchbar);
		else
			echo "<!-- SEARCH BAR NOT FOUND! -->";
		$data	=	ob_get_contents();
		ob_end_clean();
		
		if(isset($settings['write']) && $settings['write'] == true)
			echo $data;
		else
			return	$data;
	}