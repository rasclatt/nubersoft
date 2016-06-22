<?php
namespace Nubersoft;

class nObserverTemplate implements nObserver
	{
		public	static	function listen()
			{
				\nApp::nFunc()->autoload('get_site_options',NBR_FUNCTIONS);
				\nApp::saveSetting('system', array("site"=>get_site_options()));
			}
	}