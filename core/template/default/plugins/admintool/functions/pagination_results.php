<?php

function pagination_results($nApp,$layout = 'index')
	{
		$page	=	$layout.'.php';
		$exists	=	$nApp->templatePluginExists($page);
		if(!$exists)
			$page	=	'index.php';
		
		return $nApp->useData($nApp->getDataNode('pagination'))
					->useTemplatePlugin('admintool_layouts', $page);
	}