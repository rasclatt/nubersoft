<?php
	
function javascript_expire_bar($settings = false)
	{
		$js	=	nApp::jsEngine();
		
		return $js	->makeExpireBar($settings)
					->getResults();
	}