<?php
/*Title: render_contentcached()*/
/*Description: This is one of the two main rendering engines. This engine encapsulates `render_content()` however, this will allow for caching the content.*/
use \Nubersoft\nApp as nApp;

function render_contentcached()
{
	$nApp		=	nApp::call();
	# This is the normal renderer function
	$nApp->autoload(array('render_content'));
	# See if the page is supposed to be cached
	$isCached	=	($nApp->getPageURI("auto_cache") == 'on');
	# If cache is on, initiate the cache builder
	if($isCached && !$nApp->isAdmin()) {
		/*
		**	Return the include or newly rendered file
		**	Settings:
		**	Initialize-> set the type of extension to save to cache
		**	RenderDocument-> takes the block of html and if there is a file with it's name, it includes
		**	If there is no document with it's name, then it will render, save to disk, include
		**	data-> Data is the output buffered final
		*/
		$Cache	=	$nApp->getHelper('nCache');
		$Cache->cacheBegin($nApp->getStandardPath(DS.'content.html'));
		if(!$Cache->isCached())
			echo render_content();
		# Render final output
		return $Cache->cacheRender();
	}
	# If no caching required, just return html block
	else {
		if($isCached)
			$nApp->toMsgAdminAlert('Page is cached, remember to refresh if changes made.');

		return render_content();
	}
}