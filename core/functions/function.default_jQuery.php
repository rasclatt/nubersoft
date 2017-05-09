<?php
/*Title: default_jQuery()*/
/*Description: This function will insert the default <head> html for the `jQuery` libraries.*/

	function default_jQuery($force = false)
		{
			$js	=	nApp::jsEngine();
			
			return $js	->defaultJQuery(array("force_ssl"=>$force))
						->getResults();
		}