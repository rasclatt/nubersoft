<?php
	class Nuberizer
		{
			protected	$autoloader,
						$head,
						$mast,
						$menu,
						$login,
						$body,
						$foot,
						$page,
						$login_valid,
						$page_view,
						$timezone,
						$TimeZone,
						$layout,
						$error;
			
			private		$nRegister;
			
			public	function __construct()
				{
					\nApp::nFunc()->autoload('display_autoloaded',NBR_FUNCTIONS);
				}
			
			public	function core()
				{
					// Try and fetch a workflow for loading from the configs array
					$workflow	=	\nApp::nFunc()->getMatchedArray(array('workflow','onload'));
					// Load in some preferences
					$this->runDefaultLoad();
					// Do onload automation
					\nApp::nAutomator()->doWorkFlow($workflow);
					// Apply site-live
					if(!nApp::siteLive() && !is_admin()) {
						/* This will overwrite the site live message with the not found message
						** When javascript tries to pull a page silently that doesn't exist, if a fetch_token()
						** is being called, it will reset that token by reloading the page.
						** In this case, we want to show the not-found page rather than the site offline page.
						*/
						\Nubersoft\nObserverProcessor::createApp('offline');
					}
					else {
						//$this->page	=	CoreMySQL::$CoreAttributes;
						\Nubersoft\nObserverProcessor::createApp('template');
					}
				}
			
			private	function runDefaultLoad()
				{
					// Autoload functions
					AutoLoadFunction('autoload_core_functions');
					// Autoload function
					autoload_core_functions('nuber');
				}
		}