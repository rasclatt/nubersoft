<?php
/*Title: render_content()*/
/*Description: This will render the contents of the page. To allow caching, you need only wrap this function in the `render_contentcached()` function.*/
/*Example: 
	`echo render_content();`
*/
	function render_content()
		{
			
			// Validate render checks the health of the database
			AutoloadFunction('validate_render');
			// If database is up and running, continue to render page
			if(validate_render()) {	
				// If the page is valid continue to render the recursive html
				if(nApp::getPage('page_valid')) {
					ob_start();
					core::execute();
					$data	=	ob_get_contents();
					ob_end_clean();
					
					return $data;
				}
			}
		}