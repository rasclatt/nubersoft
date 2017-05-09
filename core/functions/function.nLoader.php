<?php
/*Title: nloader()*/
/*Description: This function is used to autoload required classes using the `spl_autoload_register()` function in the `config.php` file.*/
// Autoload class
function nLoader($className)
	{
		if(class_exists($className)) {
			return;
		}

		if(strpos($className,'\\') !== false) {
			if(!class_exists('Nubersoft\nAutoloadAsset')) {
				include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nAutoloadAsset.php');
			}
			$nAssets	=	new Nubersoft\nAutoloadAsset();
			$paths		=	$nAssets->stripPath($className)->getPaths();
			$fPath		=	$paths->raw_path;
			$fPathCore	=	$paths->class_path;
			if(is_file($includer = NBR_NAMESPACE_CORE.DS.$fPathCore))
				require_once($includer);
			if(is_file($includer = NBR_NAMESPACE_CORE.DS.$fPath))
				require_once($includer);
			elseif(is_file($includer = NBR_CLIENT_DIR.DS.'plugins'.DS.$fPathCore))
				require_once($includer);
			elseif(is_file($includer = NBR_CLIENT_DIR.DS.'plugins'.DS.$fPath))
				require_once($includer);
			else {
				if(!class_exists('Nubersoft\configFunctions')) {
					include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'configFunctions.php');
					include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nFunctions.php');
				}
				$nAssets	->useLocation(NBR_CLIENT_DIR)
							->autoload($className);
			}
			return;
		}
		elseif(is_file(NBR_CLASS_CORE.DS.$className.'.php')) {
			$includer	=	NBR_CLASS_CORE.DS.$className.'.php';
		}
		elseif(is_file(NBR_CLIENT_DIR.DS."classes".DS.$className.'.php'))
			$includer	=	NBR_CLIENT_DIR.DS."classes".DS.$className.'.php';
		
		if(!empty($includer) && is_file($includer)) {
				require_once($includer);
			}
		elseif(!empty($includerAlt) && is_file($includerAlt))
			require_once($includerAlt);
	}