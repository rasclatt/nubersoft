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
				include(NBR_NAMESPACE_CORE._DS_.'Nubersoft'._DS_.'nAutoloadAsset.php');
			}
			$nAssets	=	new Nubersoft\nAutoloadAsset();
			$paths		=	$nAssets->stripPath($className)->getPaths();
			$fPath		=	$paths->raw_path;
			$fPathCore	=	$paths->class_path;
			if(is_file($includer = NBR_NAMESPACE_CORE._DS_.$fPathCore))
				require_once($includer);
			if(is_file($includer = NBR_NAMESPACE_CORE._DS_.$fPath))
				require_once($includer);
			elseif(is_file($includer = NBR_CLIENT_DIR._DS_.'plugins'._DS_.$fPathCore))
				require_once($includer);
			elseif(is_file($includer = NBR_CLIENT_DIR._DS_.'plugins'._DS_.$fPath))
				require_once($includer);
			else {
				if(!class_exists('Nubersoft\configFunctions')) {
					include(NBR_NAMESPACE_CORE._DS_.'Nubersoft'._DS_.'configFunctions.php');
					include(NBR_NAMESPACE_CORE._DS_.'Nubersoft'._DS_.'nFunctions.php');
				}
				$nAssets	->useLocation(NBR_CLIENT_DIR)
							->autoload($className);
			}
			return;
		}
		elseif(is_file(NBR_CLASS_CORE._DS_.$className.'.php')) {
			$includer	=	NBR_CLASS_CORE._DS_.$className.'.php';
		}
		elseif(is_file(NBR_CLIENT_DIR._DS_."classes"._DS_.$className.'.php'))
			$includer	=	NBR_CLIENT_DIR._DS_."classes"._DS_.$className.'.php';
		
		if(!empty($includer) && is_file($includer)) {
				require_once($includer);
			}
		elseif(!empty($includerAlt) && is_file($includerAlt))
			require_once($includerAlt);
	}