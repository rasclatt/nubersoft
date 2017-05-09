<?php
	class	CoreMySQL
		{
			public		static	$CoreAttributes;
			protected	static	$con;
			
			private	function __construct()
				{
				}
			
			public	static	function Initialize($_page = true)
				{
					if(DatabaseConfig::$con) {
						// Check to see if the site has been turned off or not
						// If site live not on
						if(!nApp::siteLive()){
							// Connect to remote database
							if(is_admin()) {
								$_createTable	=	FetchRemoteTable::Create('system_settings');
								$site_live		=	new CheckVitals();
								$checksite_live	=	$site_live->SiteStatus()->live_status;
							}
							
							if(isset($checksite_live)) {
								if(!NubeData::$settings->user->loggedin && !is_admin()) {
									$verbage	=	$checksite_live[0];
									
									AutoloadFunction('silent_error');
									silent_error();
									
									if(!empty(NubeData::$settings->error404)) {
										AutoloadFunction('render_error');
										render_error();
									}
									else {
										AutoloadFunction('get_errorpage_temp');
										get_errorpage_temp();
									}

									exit;
								}
							}

							// Check for first time install HTACCESS FILE.
							// If not there but there is content for one, create it.
							if(!NubeData::$settings->engine->htaccess) {
								AutoloadFunction('get_default_htaccess');
								NubeData::$settings->engine->htaccess	=	get_default_htaccess(array("write"=>true));
							}
						}
							
						// Fetch the page results
						$result	=	self::FetchPage();
						// If the statement produces results build variables
						if($result != 0) {
							global $unique_id;
							$unique_id				=	$result['unique_id'];
							self::$CoreAttributes	=	$result;
						}
					}
					
					self::$CoreAttributes	=	(isset(self::$CoreAttributes))? self::$CoreAttributes : array();
				}
				
			protected	static	function FetchPage()
				{
					$result	=	nApp::getPageURI();
					
					// If the page could not be matched, make sure table exists
					if(!$result) {
						if(is_admin()) {
							$_createTable	=	FetchRemoteTable::Create('main_menus');
						}

						$_incidental['404']		=	true;
						$result['page_valid']	=	$result['unique_id']	=	false;
						$result['page_live']	=	$result['auto_cache']	=	$result['session_status']	=	'off';
					}
					else
						$result['page_valid']	=	true;
					
					return $result;
				}
		}