<?php
	function render_masthead()
		{
			
			AutoloadFunction('get_site_prefs,backtrace_file');
		
			$mast_local	=	backtrace_file(true);
			$content	=	nApp::getSitePrefs();
			$bypass		=	(isset($content->site->content->head))? $content->site->content->head:false;
					
			if(!empty($mast_local[1]['file']) && is_file($mastfile = dirname($mast_local[1]['file'])."/masthead.php")) {
					ob_start();
					include($mastfile);
					$data	=	ob_get_contents();
					ob_end_clean();
					$layout['content']		=	$data;
					$layout['page_live']	=	"on";
				}
			else {
					
					if(isset($content->header->content->html->toggle) && $content->header->content->html->toggle == 'on') {
							$layout['content']		=	$content->header->content->html->value;
							$layout['page_live']	=	"on";
						}
					else {
							$layout['content']		=	false;
							$layout['page_live']	=	"off";
						}
				}
			
			$layout		=	(object) $layout;
			
			ob_start();
			core::Header($layout,$bypass);
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}