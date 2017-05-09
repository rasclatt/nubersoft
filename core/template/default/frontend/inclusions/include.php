<?php
// Autosave a js file header js
if(!is_file($incJs = NBR_CLIENT_DIR.DS.'js'.DS.'inclusions.js') && function_exists('render_javascript')) {
	ob_start();
	$content	=	render_javascript(javascript_expire_bar($expirebar).PHP_EOL).PHP_EOL;
	$this->jsEngine()->addScript($content)
					->saveDocument($incJs);
	ob_end_clean();
}