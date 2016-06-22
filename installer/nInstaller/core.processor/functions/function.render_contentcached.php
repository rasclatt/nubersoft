<?php
/*Title: render_contentcached()*/
/*Description: This is one of the two main rendering engines. This engine encapsulates `render_content()` however, this will allow for caching the content.*/
function render_contentcached()
	{
		// This is the normal renderer function
		AutoloadFunction('compare,render_content');
		// If cache is on, initiate the cache builder
		if(compare(nApp::getPage("auto_cache"),'on')) {
			// Return the include or newly rendered file
			// Settings:
			// Initialize-> set the type of extension to save to cache
			// RenderDocument-> takes the block of html and if there is a file with it's name, it includes
			// If there is no document with it's name, then it will render, save to disk, include
			// data-> Data is the output buffered final
			return CacheEngine::app()	->Initialize("html")
										->RenderDocument(render_content())
										->getCached();
		}
		// If no caching required, just return html block
		else
			return render_content();
	}