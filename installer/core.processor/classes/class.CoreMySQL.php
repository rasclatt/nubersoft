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
							elseif($checksite_live) {
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
					AutoloadFunction('nQuery');
					// Set Directory and Query string
					if(isset($_SERVER['SCRIPT_URL']))
						$query_uri	=	$_SERVER['SCRIPT_URL'];
					elseif(isset($_SERVER['REDIRECT_URL']))
						$query_uri	=	$_SERVER['REDIRECT_URL'];
					else
						$query_uri	=	"/";
					
					$uri['subdir']	=	str_replace("//","/",str_replace("//","/",preg_replace("/[^0-9a-zA-Z\_\-\/]/","",$query_uri)));
					$uri['query']	=	str_replace("//","/","/".preg_replace("/([^\?]{1,})\?([^\?]{1,})/","$2",$_SERVER['REQUEST_URI'])."/");
					$uri['query']	=	($uri['subdir'] == $uri['query'])? false : trim($uri['query'],"/");
					// If only the forward slash is remaining, then that indicates home page
					// Because of the way the page builder rebuilds the paths, the home will fail
					$homefind		=	($uri['subdir'] == '/')? array("is_admin"=>2) : array("full_path"=>$uri['subdir']);
					// Fetch the path from the database
					$base			=	nQuery()	->select()
													->from("main_menus")
													->where($homefind)
													->fetch();
					// If path is found			
					$result			=	($base != 0)? $base[0] : false;
					// If the page could not be matched, make sure table exists
					if($result == false) {
						if(is_admin()) {
							$_createTable	=	FetchRemoteTable::Create('main_menus');
						}
	
						global	$_incidental;
						$_incidental['404']		=	true;
						$result['page_valid']	=	$result['unique_id']	=	false;
						$result['page_live']	=	$result['auto_cache']	=	$result['session_status']	=	'off';
					}
					else
						$result['page_valid']	=	true;
					
					return $result;
				}
		}