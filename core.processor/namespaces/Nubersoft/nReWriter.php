<?php
namespace Nubersoft;

class nReWriter
	{
		private static	$singleton;
		public	function __construct()
			{
				if(self::$singleton instanceof nReWriter)
					return self::$singleton;
				
				self::$singleton	=	$this;
			}
		
		public	static	function validate()
			{
				// If the main htaccess is not found
				if(!is_file(NBR_ROOT_DIR._DS_.'.htaccess')) {
					// Autoload functions to create
					\nApp::nFunc()->autoload(array("get_default_htaccess","get_site_prefs"),NBR_FUNCTIONS);
					// Try and get a database-saved version
					$getprefs	=	\nApp::getSitePrefs();
					// If there is one, create from that
					if(!empty($getprefs->site->content->htaccess))
						self::getDefault(array('htaccess'=>$getprefs->site->content->htaccess,'write'=>true));
					// Create default htaccess file
					else
						self::getDefault(array('write'=>true));
				}
			}
		
		public	static	function getDefault($settings = false)
			{
				// Set default location for htaccess file (root)
				$dFile	=	__DIR__._DS_.'nReWriter'._DS_.__FUNCTION__._DS_.'htaccess.txt';
				// If there is a script already set use it or else use default
				$data	=	(!empty($settings['htaccess']))? $settings['htaccess'] : file_get_contents($dFile);
				// If the write is set, try and use the script and write file to disk
				if(!empty($settings['write'])) {
					// Save into the root drive (or where ever indicated by 'dir')
					$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_ROOT_DIR;
					$wSettings	=	array(
										'dir'=>$dir,
										"filename"=>'.htaccess',
										"payload"=>$data,
										"write"=>'w'
									);
					// Write file, return script
					return \SaveToDisk::Write($wSettings);
				}
				// Return the script
				return $data;
			}
	}