<?php
	function header_download($file = false, $settings = false)
		{
			// If the file is not real just stop
			if(!is_file($file))
				return false;
			// If there are header settings to override standard
			if(is_array($settings) && !empty($settings)) {
					// Loop through those
					foreach($settings as $name => $vals) {
							// If left blank, just skip to next key/val pair
							if($vals === "")
								continue;
							// If the name is a numeric (non-associative)
							// just output the value
							if(is_numeric($name))
								header($vals);
							else {
									// If name is associative
									// Use the $name as title, implode values
									$useval	=	(is_array($vals))? "{$name}: ".implode(", ",$vals) : "{$name}: {$vals}";
									header($useval);
								}
						}
				}
			// Just use standard downloading
			else {
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-type: application/octet-stream'); 
					header('Content-Transfer-Encoding: binary'); 
					header('Connection: Keep-Alive');
					header('Expires: 0');
					header('Pragma: public');
					header('Content-length: '.filesize($file)); 
					header('Content-disposition: attachment; filename="'.basename($file).'"'); 
				}
				
			readfile($file);
			return true;
		}