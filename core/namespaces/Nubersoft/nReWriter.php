<?php
namespace Nubersoft;

class nReWriter extends \Nubersoft\nFunctions
	{
		public	static	function validate()
			{
				# If the main htaccess is not found
				if(!is_file(NBR_ROOT_DIR.DS.'.htaccess')) {
					# Autoload functions to create
					nApp::call()->autoload(array("get_default_htaccess","get_site_prefs"));
					# Try and get a database-saved version
					$getprefs	=	nApp::call()->getSitePrefs();
					# If there is one, create from that
					if(!empty($getprefs->site->content->htaccess))
						self::getDefault(array('htaccess'=>$getprefs->site->content->htaccess,'write'=>true));
					# Create default htaccess file
					else
						self::getDefault(array('write'=>true));
				}
			}
		
		public	static	function __callStatic($name,$args = false)
			{
				$settings	=	(!empty($args[0]))? $args[0] : false; 
				# Set default location for htaccess file (root)
				$dFile	=	__DIR__.DS.'nReWriter'.DS.$name.DS.'htaccess.txt';
				$getDf	=	(is_file($dFile))? file_get_contents($dFile) : false;
				if(empty($getDf))
					throw new \Nubersoft\nException('.htaccess type missing: "'.$name.'. Check your descriptions are loaded in /core/namespaces/nReWriter/"',404002);
				# If there is a script already set use it or else use default
				$data	=	(!empty($settings['htaccess']))? $settings['htaccess'] : $getDf;
				# If the write is set, try and use the script and write file to disk
				if(!empty($settings['write']) && !empty($data)) {
					$nApp		=	nApp::call();
					# Save into the root drive (or where ever indicated by 'dir')
					$dir		=	(!empty($settings['dir']))? $settings['dir'] : NBR_ROOT_DIR;
					$wSettings	=	array(
										"save_to"=>$nApp->toSingleDs($dir.DS.'.htaccess'),
										"content"=>$data,
										"overwrite"=>true
									);
					try {
						$nApp->getHelper('nFileHandler')->writeToFile($wSettings);
					}
					catch (nException $e) {
						if($nApp->isAdmin()) {
							$e->getMessage();
							die($e->getMessage());
						}
						else {
							$nApp->saveToLogFile(array(
								'filename'=>'nException'.DS.__FUNCTION__,
								'path'=>NBR_SETTINGS.DS.'exceptions'.DS
							),$e->getMessage().strip_tags(printpre($dir,'{backtrace}')));
						}
					}
					return $data;
				}
				# Return the script
				return $data;
			}
		
		public	function createHtaccess($settings = false)
			{
				$script	=	(!empty($settings['content']))? $settings['content']: false;
				$dir	=	(!empty($settings['save_to']))? $settings['save_to']: false;
				$rule	=	(!empty($settings['rule']))? $settings['rule']: 'server_rw';
				$mkdir	=	(!empty($settings['make']))? $settings['make']: false;
				$write	=	(!empty($settings['write']))? $settings['write']: false;
				
				$writer['server_rw']	=	"serverReadWrite";
				$writer['default']		=	"getDefault";
				$writer['server_r']		=	"serverRead";
				$writer['browser_r']	=	"browserRead";
		
				if(!isset($writer[$rule]) && !$script)
					return false;
				
				if($script) {
					$content	=	$script;
					nApp::call()->getHelper('nFileHandler')->writeToFile(array(
						'content'=>$content,
						'save_to'=>nApp::call()->toSingleDs($dir.DS.'.htaccess'),
						'security'=>false,
						'overwrite'=>true,
						'make'=>$mkdir
					));
				}
				elseif(isset($writer[$rule])) {
					$method		=	$writer[$rule];
					if($write)
						self::$method(array(
							'dir'=>nApp::call()->toSingleDs($dir.DS),
							'security'=>false,
							'overwrite'=>true,
							'write'=>true
							)
						);
					else
						self::$method();
				}
			}
		
		public	function fetchHtaccess()
			{
				$fetch	=	nApp::call()->nQuery()
								->select("content")
								->from("system_settings")
								->where(array("name"=>"settings","page_element"=>"settings_site"))
								->fetch();
				
				if(isset($fetch[0]['content']))
					return nApp::call('Safe')->decode($fetch[0]['content']);
			}
		
		public	function getScript($type)
			{
				if(is_file($htaccess = __DIR__.DS.'nReWriter'.DS.$type.DS.'htaccess.txt'))
					return file_get_contents($htaccess);
			}
	}