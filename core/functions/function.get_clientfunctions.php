<?php
/*Title: get_clientfunctions*/
/*Description: This function auto-includes any functions in the `/client_assets/settings/functions/` folder. This function runs in the `config.php` file at first load.*/

	function get_clientfunctions($dir = false)
		{
			
			
			if(!function_exists("load_user_functions")) {
					
					function load_user_functions($funcDir = false)
						{
							
							
							if(is_dir($funcDir)) {
									$funcFilter	=	array('.','..');
									$funcSearch	=	scandir($funcDir);
									$funcDiff	=	array_diff($funcSearch,$funcFilter);
									
									if(!empty($funcDiff)) {
											foreach($funcDiff as $funcIncludes) {
													if(preg_match('/\.htm|\.php|\.html$/',$funcDir.$funcIncludes))
														include_once($funcDir.$funcIncludes);
												}
										}
								}
						}
				}
			
			if(is_array($dir)) {
					foreach($dir as $loadFolder)
						load_user_functions($loadFolder);
				}
			else {
					$funcDir	=	($dir != false)? $dir : NBR_CLIENT_DIR.'/settings/functions/';
					load_user_functions($funcDir);
				}
		}
?>