<?php
	function render_meta()
		{
			// Get the current loading page
			// Convert it to an array
			$page	=	Safe::to_array(nApp::getPage());
			// See if the page options are empty or not
			if(!isset($page['page_options']))
				return;
			// Process the json meta
			$procMeta	=	function($str)
				{
					// Decode the with json
					$meta	=	json_decode(Safe::decodeSingle($str),true);
					// If there is no meta, just skip
					if(!is_array($meta))
						return false;
					// Loop through each option
					foreach($meta['meta'] as $key => $value) {
						// If the value is empty, skip it
						if(empty($value))
							continue;
		
						$nMeta[$key]	=	$value;
					}
					
					return (!empty($nMeta))? $nMeta : false;
				};
			// See if this page has any meta
			$nMeta	=	$procMeta($page['page_options']);
			// If empty and the page is not the home page
			if(empty($nMeta) && nApp::getPage('is_admin') !== 2) {
				// Get the meta
				$nMeta	=	(!empty(nApp::getHomePage()->page_options))? $procMeta(nApp::getHomePage()->page_options) : false;
			}
			// If the meta is empty after all that, just return
			if(empty($nMeta))
				return false;
			// Write the meta
			foreach($nMeta as $key => $value) {
			// Start the buffer
			ob_start();
				if(strpos($value,'~') !== false) {
?><meta <?php echo Safe::decode(trim($value,'~')); ?> />
<?php			}
				else {		
					// Write the meta
?><meta name="<?php echo strtolower($key); ?>" content="<?php echo $value; ?>" />
<?php			}
			}
			
			$data	=	ob_get_contents();
			ob_end_clean();
			
			if(empty($data)) {
				if(nApp::getPage('is_admin') !== 2) {
					echo nApp::getHomePage()->page_options;
				}
			}
			
			return $data;
		}