<?php
function nloader($className)
	{
		# Default core path
		$corePath		=	NBR_NAMESPACE_CORE;
		# Create a default spot for the apps
		$clientPath		=	NBR_CLIENT_DIR.DS.'apps';
		# Client path to file
		$corePathFile	=	$corePath.DS.trim(str_replace('\\',DS,$className),DS).'.php';
		# Client path to file
		$clientPathFile	=	$clientPath.DS.trim(str_replace('\\',DS,$className),DS).'.php';
		# Check if file in default spot
		if(is_file($clientPathFile)) {
			# Include once
			require_once($clientPathFile);
			# Stop
			return;
		}
		# Check if the file is in the core
		elseif(is_file($corePathFile)) {
			# Include once
			require_once($corePathFile);
			# Stop
			return;
		}
		# Start the complex autoloader
		if(strpos($className,'\\') !== false) {
			if(!class_exists('Nubersoft\nAutoloadAsset')) {
				foreach(array('Singleton','nFunctions','nApp','nHtml','nImage','nAutoloadAsset') as $class){
					$inc	=	NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS."{$class}.php";
					include_once($inc);
				}
			}
			
			$nAssets	=	new \Nubersoft\nAutoloadAsset(new \Nubersoft\nHtml());
			$paths		=	$nAssets->stripPath($className)->getPaths();
			$fPath		=	$paths->raw_path;
			$fPathCore	=	$paths->class_path;
			
			if(is_file($includer = NBR_NAMESPACE_CORE.DS.$fPathCore))
				require_once($includer);
			
			if(is_file($includer = NBR_NAMESPACE_CORE.DS.$fPath))
				require_once($includer);
			else {
				if(!class_exists('Nubersoft\configFunctions')) {
					include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'configFunctions.php');
					include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nFunctions.php');
				}
				
				$nAssets	->useLocation(NBR_CLIENT_DIR)
							->autoloadClass($className);
			}
			
			return;
		}
		
		if(!empty($includer) && is_file($includer)) {
			require_once($includer);
		}
		elseif(!empty($includerAlt) && is_file($includerAlt)) {
			require_once($includerAlt);
		}
	}