<?php
//$cache->checkCacheFile(nApp::getCacheFolder().DS.'template'.DS.'footer.html')->startCaching();
//if($cache->allowRender()) {
	echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\View')->renderFooter();
	echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\JsLibrary')->nScroller();
//}
//$cache->endCaching()->addContent($cache->getCached())->renderBlock();
//echo nApp::getQueryCount();
//autoload_function("delete_contents");
//delete_contents(nApp::getCacheFolder().DS.'template'.DS);