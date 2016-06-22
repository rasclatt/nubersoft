<?php
/*Title: nloader()*/
/*Description: This function is used to autoload required classes using the `spl_autoload_register()` function in the `config.php` file.*/
	// Autoload class
	function nLoader($className) {
			if(class_exists($className)) {
				return;
			}
			if(strpos($className,'\\') !== false) {
				$pathwork	=	explode("\\",trim($className,'\\'));
				$pathwork	=	array_filter($pathwork);
				$filename	=	array_pop($pathwork);
				$path		=	array_map(function($val) {
												return strtolower($val);
											},$pathwork);
											
				$includer	=	CLIENT_DIR."/plugins/".implode("/",$path)."/".$filename.'.php';
			}
			elseif(is_file(CLASS_CORE."/class.".$className.'.php')) {
				$includer	=	CLASS_CORE."/class.".$className.'.php';
			}
			elseif(is_file(CLIENT_DIR."/classes/".$className.'.php'))
				$includer	=	CLIENT_DIR."/classes/".$className.'.php';
			
			if(!empty($includer) && is_file($includer))
				include_once($includer);
			elseif(!empty($includerAlt) && is_file($includerAlt))
				include_once($includerAlt);
		}