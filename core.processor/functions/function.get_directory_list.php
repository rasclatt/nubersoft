<?php
	function get_directory_list($settings = false)
		{
			AutoloadFunction('check_empty');
			$directory		=	(!empty($settings['dir']))? $settings['dir']:NBR_CLIENT_DIR."/";
			$encode			=	check_empty($settings,'enc',true);
			$filetype		=	(!empty($settings['type']) && is_array($settings['type']))? $settings['type']:false;
			$recursive		=	(isset($settings['recursive']) && $settings['recursive'] == false)? false : true;
			$addpreg		=	($filetype != false)? "\.".implode("|\.",$filetype):"\.php|\.csv|\.txt|\.htm|\.css|\.htm|\.js";
			
			$array			=	array();
			$array['dirs']	=	array();
			$array['host']	=	array();
			$array['root']	=	array();
			
			if(!is_dir($directory))
				return false;
			
			if(!$recursive) {
					AutoloadFunction("get_nonrecurse_dir");
					$array	=	get_nonrecurse_dir($directory,$addpreg);
					return $array;
				}
			
			$dir			=	new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory),RecursiveIteratorIterator::CHILD_FIRST);
					
			// Loop through directories
			while($dir->valid()) {
					// If there is a specific value to return
					$render	=	($filetype == false);
					
					try {
							$file = $dir->current();
							
							ob_start();
							echo $file;
							$data	=	ob_get_contents();
							ob_end_clean();
							
							$data	=	trim($data);
							// Search for files and folders
							if(preg_match('/'.$addpreg.'$/',basename($data),$ext)) {
									// If there is an array to return for file type and a match is found
									if($filetype != false && isset($ext[0]))
										$render	=	(in_array(ltrim($ext[0],"."),$filetype));
									
									if($render)
										$array['list'][]	=	($encode)? urlencode(Safe::encode(base64_encode($data))):$data;
								}
							
							if($render) {
									if(basename($data) != '.' && basename($data) != '..') {
											$array['host'][]	=	$data;
											$array['root'][]	=	str_replace(NBR_ROOT_DIR,"",$data);
											if(is_dir($data) && !in_array($data."/",$array['dirs'])) {
													$array['dirs'][]	=	$data."/";
												}
										}
								}
							
							unset($data);
							
							$dir->next();
						}
					catch (UnexpectedValueException $e) {
							continue;
						}
				}
			
			return (isset($array))? $array:false;
		}
?>