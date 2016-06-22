<?php
// Autosave a js file header js
if(!is_file($incJs = NBR_CLIENT_DIR._DS_.'js'._DS_.'inclusions.js')) {
	ob_start();
	$content	=	render_javascript(javascript_expire_bar($expirebar).PHP_EOL).PHP_EOL;
	nApp::jsEngine()->addScript($content)
					->saveDocument($incJs);
	ob_end_clean();
}