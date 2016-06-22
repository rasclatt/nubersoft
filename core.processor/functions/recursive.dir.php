<?php	
	// Format tree into nested
	function buildRecDirs($curr, $info, $classScript)
		{
			
			
			if(is_array($curr))
				{
					// Run wrapper script
				//	echo '<div style="display: block; margin: 5px; padding: 5px; border: 1px solid #06C;">';
				//	echo 'Script runner 1 =>';
					
					foreach($curr as $subkeys => $subvalues)
						{
							$buildPages	=	(!isset($buildPages))? new siteBuild(): '';
							$buildPages->setFileTree($info[$subkeys], 'link', $buildPages->directory);
							
                           	if(is_array($subvalues))
                                {
									// Run primary script for info
								//	echo '<div style="display: block; margin: 5px; padding: 5px; border: 1px solid #0c3;">';
								//	echo 'Script runner 2 =>';
								//	print_r($info[$subkeys]);
                                    buildRecDirs($subvalues, $info,  $classScript);
								//	echo '</div>';
                                }
							else
								{
									// Run primary script for info
								//	echo '<div style="display: block; margin: 5px; padding: 5px; border: 1px solid #1C3;">';
								//	echo 'Script runner 3 =>';
								//	print_r($info[$subkeys]);
							//		echo '</div>';
								}
						}
						
					return $buildPages->directory;
				//	echo '</div>';
				}
		} ?>