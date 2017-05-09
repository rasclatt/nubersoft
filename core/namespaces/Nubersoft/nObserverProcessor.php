<?php
namespace Nubersoft;

class nObserverProcessor implements nObserver
	{
		public	static	function listen()
			{
				// Load the standard engines
				$nRequester	=	new \Nubersoft\nRequester(
										new \Nubersoft\nConfigEngine(
											new \Nubersoft\configFunctions(
												new \Nubersoft\nAutomator()),
											new \Nubersoft\nFunctions()
										)
									);
					
				$nRequester->execute();
			}
		
		public	static	function offline()
			{
				if(!empty(\NubeData::$settings->error404)) {
					\nApp::nFunc()->autoload('render_error',NBR_FUNCTIONS);
					echo render_error();
				}
				else {
					\nApp::nFunc()->autoload('get_errorpage_temp',NBR_FUNCTIONS);
					echo get_errorpage_temp();
				}
			}
		
		public	static	function createApp($name = 'default')
			{
				\NuberEngine::getApplication(
					new \Nubersoft\configFunctions(
						new \Nubersoft\nAutomator()),
					new \Nubersoft\nRegister(),
					$name
				);
			}
		
		public	static	function loadCoreFunctions($load = 'config')
			{
				$processor	=	function($array) {
		
					foreach($array as $func => $path) {
						if(empty($path))
							$auto['autoload'][]	=	$func;
						else
							$auto[$path][]	=	$func;
					}
						
					if(!empty($auto)) {
						if(!empty($auto['autoload'])) {
							AutoloadFunction(implode(",",$auto['autoload']));
							unset($auto['autoload']);
						}
						
						if(!empty($auto)) {
							foreach($auto as $link => $func) {
								\nApp::nFunc()->autoload(implode(",",$func),$link);
							}
						}
					}
				};
				
				$prefs	=	\nApp::getRegistry();

				if($load == 'config' && !empty($prefs['onloadconfigfunctions'])) {
					$processor($prefs['onloadconfigfunctions']);
				}
				
				if($load == 'nuber' && !empty($prefs['onloadnuberfunctions'])) {
					$processor($prefs['onloadnuberfunctions']);
				}
			}
		
		public	function loadClientConfig()
			{
				$client_config = NBR_CLIENT_DIR.DS.'settings'.DS.'config-client.php';
				\nApp::nFunc()->autoload('is_admin',NBR_FUNCTIONS);
				// This will check if there is a reset command set
				if(!empty($_GET['command']) && $_GET['command'] == 'client_config') {
					// If there is and the user is an admin
					// Start session for reset purposes
					session_start();
					if(is_admin()) {
						// Try and create a file
						\nApp::nFunc()->autoload('create_client_config',NBR_FUNCTIONS);
						create_client_config();
						$try	=	true;
					}
				}
				
				if(is_file($client_config)) {
					include_once($client_config);
					
					if(!empty($try)) {
						if(is_admin()) {
							\nApp::saveIncidental("client_config","Client Config Added");
						}
					}
						
					return true;
				}
				else {
					// Return false if this effort has alredy been tried before.
					if($break) {
						session_start();
						$msg	=	'Failed to load client config.';
						\nApp::saveIncidental("client_config",$msg);
						if(is_admin()) {
							throw new \Exception($msg);
						}
							
						return false;
					}
					\nApp::nFunc()->autoload(array('create_client_config','load_client_config'),NBR_FUNCTIONS);
					create_client_config();
					// Try loading page again, set break this time
					load_client_config(true);
				}
			}
		
		public	function getCachedPrefs($dir)
			{
				$conf		=	function($filename)
					{
						if(!is_file($filename))
							return false;
						
						$array	=	file_get_contents($filename);
						return json_decode($array,true);
					};
				$dir		=	rtrim($dir,DS);	
				$xml		=	$conf($dir.DS.'xml_add_list.json');
				$configs	=	$conf($dir.DS.'configs.json');
				if(is_array($xml))
					\nApp::saveSetting('xml_add_list',$xml);
				if(is_array($configs))
					\nApp::saveSetting('configs',$configs);
			}
		
		public	function setPresets()
			{
				$settings['engine']['openssl_salt']		=	(defined("OPENSSL_SALT"))? OPENSSL_SALT: "1029374537280172";
				$settings['engine']['openssl_iv']		=	(defined("OPENSSL_IV"))? OPENSSL_IV: "0192472903847283";
				$settings['engine']['file_salt']		=	(defined("FILE_SALT"))? FILE_SALT: "saltstash";
				// TEMPLATE: Error page layout (requires full file path)
				$settings['site']['error_404']			=	NBR_TEMPLATE_DIR.DS."default".DS."site.error404.php";
				// Default 
				$settings['site']['template_folder']	=	NBR_TEMPLATE_DIR.DS."default".DS;
				// Default header
				$settings['site']['template_head']		=	NBR_TEMPLATE_DIR.DS."default".DS;
				// Layout for the prefs page template
				$settings['site']['system_prefs']		=	(!defined("SYS_PREFS_TEMP") || (defined("SYS_PREFS_TEMP") && !is_file(SYS_PREFS_TEMP)))? DS."ajax".DS."form.site.prefs.php":SYS_PREFS_TEMP;
				// Save folder for tempfiles
				$settings['site']['temp_folder']		=	(!defined("TEMP_DIR"))? NBR_ROOT_DIR.DS.'..'.DS.'temp'.DS:NBR_ROOT_DIR.TEMP_DIR;
				// Save cache folder
				$settings['site']['cache_folder']		=	(!defined("CACHE_DIR"))? NBR_ROOT_DIR.DS.'..'.DS.'cache'.DS:NBR_ROOT_DIR.CACHE_DIR;
				// This is overwritten on latter template retrieval. This is just default
				$settings['site']['template']			=	'default'.DS.'template';
				
				\nApp::saveSetting('site',$settings['site']);
				\nApp::saveSetting('engine',$settings['engine']);
			}
	}