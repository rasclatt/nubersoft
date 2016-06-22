<?php
/*Title: BuildRecursiveDirs()*/
/*Description: This function will build folders recursively.*/
/*Alert: `This function is deprecated.`*/

	function BuildRecursiveDirs($curr, $info, $classScript)
		{
			
			if(is_array($curr)) {
					foreach($curr as $subkeys => $subvalues) {
							$buildPages	=	(!isset($buildPages))? new siteBuild(): '';
							$buildPages->setFileTree($info[$subkeys], 'link', $buildPages->directory);

							if(is_array($subvalues))
								BuildRecursiveDir($subvalues, $info,  $classScript);
						}
						
					return $buildPages->directory;
				}
		}
?>