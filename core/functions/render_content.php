<?php
/*Title: render_content()*/
/*Description: This will render the contents of the page. To allow caching, you need only wrap this function in the `render_contentcached()` */
/*Example: 
`echo render_content();`
*/
use Nubersoft\nApp as nApp;

function render_content()
	{
		$nApp	=	nApp::call();
		// If database is up and running, continue to render page
		if(!empty($nApp->getDataNode('connection')->health)) {	
			// If the page is valid continue to render the recursive html
			if(!empty($nApp->getDataNode('pageURI'))) {
				ob_start();
				$inc	=	$nApp->getPageURI('include');
				if(!empty($inc))
					echo $nApp->getHelper('nRender')->render($inc);
				else
					$nApp->getPlugin('\nPlugins\Nubersoft\core')->execute();
	
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		}
	}