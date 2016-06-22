<?php
function gatherSession()
	{
		$install	=	(check_empty($_SESSION,'install'))? $_SESSION['install'] : false;
		$setup		=	(check_empty($_POST,'app'))? $_POST['app']['setup'] : false;
		
		if(empty($_SESSION['install_key'])) {
			$_SESSION['install_key']['salt']	=	mt_rand(1000000000000000,1999999999999999);
			$_SESSION['install_key']['iv']		=	mt_rand(1000000000000000,1999999999999999);
		}
		
		if($setup) {
			
			parse_str(Safe::decode(urldecode($setup)),$setup);
			
			if(!empty($setup['purge'])) {
				$_SESSION['install_instruct']['purge']	=	true;
			}

			if(empty($setup['setup']) || (!empty($setup['setup']) && !is_array($setup['setup'])))
				return false;
				
			foreach($setup['setup'] as $key => $value) {
				if(!empty($value))
					$array[$key]	=	Safe::encOpenSSL($value,$_SESSION['install_key']);	
			}

		
			if(!empty($array)) {
				if(isset($array['username']) && !isset($array['n_username']))
					installSettings(json_encode($array));
				elseif(isset($array['n_username']) && !isset($array['username']))
					installSettings(json_encode($array),'api');
			}
		}
	}

function getVal($input = false,$strict = false)
	{
		if(!empty($_POST['setup'][$input]) && !is_array($_POST['setup'][$input]))
			return ((!empty($strict) && $strict == 'post') || !$strict)? htmlspecialchars(trim($_POST['setup'][$input]),ENT_QUOTES) : false;
		elseif(!empty($_SESSION['install'][$input]) && !is_array($_SESSION['install'][$input]))
			return ((!empty($strict) && $strict == 'session') || !$strict)? $_SESSION['install'][$input] : false;
		
		return false;
	}

function isValidConnect($array,&$con = false)
	{
		$creds['host']	=	(!empty($array['host']))? $array['host'] : false;
		$creds['user']	=	(!empty($array['username']))? $array['username'] : false;
		$creds['pass']	=	(!empty($array['password']))? $array['password'] : false;
		$creds['data']	=	(!empty($array['database']))? $array['database'] : false;
		$con			=	DatabaseEngine::connect(array("creds"=>$creds));
		return (!empty($con));
	}

function fetchInstaller($settings = false)
	{
		$dir		=	(!empty($settings['dir']))? $settings['dir'] : false;
		$link		=	(!empty($settings['link']))? $settings['link'] : 'http://www.nubersoft.com/client_assets/installer/nubersoft.zip';
		$filename	=	(!empty($settings['fname']))? $settings['fname'] : 'installer.zip';
		// Set destination
		$dir	=	(!empty($dir))? $dir : __DIR__.'/../tmp/';
		// Set installer name
		$new	=	str_replace("//","/",$dir.$filename);
		// Make directory
		if(!is_dir($dir))
			mkdir($dir,0777,true);
		else {
			delete_folder_contents($dir);
			mkdir($dir,0777,true);
		}
		
		// Fetch the file
		$file	=	@file_get_contents($link);
		
		if(empty($file))
			return false;
		
		// Open empty container
		$wr		=	fopen($new,'w');
		// Fill container
		fwrite($wr,$file);
		// Close container
		fclose($wr);
		// Check if container exists
		if(!is_file($new))
			return false;
			
		return $new;
	}

function unZip($filename = false,$filesave = false)
	{
		$from	=	str_replace('//','/',$filename);
		$to		=	str_replace('//','/',$filesave);
		
		if(!is_file($from))
			return false;
		
		if(!is_dir($to))
			mkdir($to,0777,true);
		
		$Zipper	=	new ZipArchive();
		
		$Zipper->open($from);
		$Zipper->extractTo($to);
		$Zipper->close();
	}

function moveFiles($from,$to)
	{
		AutoloadFunction("get_directory_list");
		$settings['type']	=	array('php','html','htm','htaccess','csv','css','js','png','jpg','jpeg','gif','xml');
		$settings['dir']	=	$from;
		$installer			=	get_directory_list($settings);
		$count				=	count($installer['list']);
		if(!empty($installer['list'])) {
			echo 'Files/Folders Anylized...<br />';
			for($i = 0; $i < $count; $i++) {
				
				$dest		=	pathinfo(str_replace($from,"",$installer['list'][$i]));
				$baseDir	=	$to.$dest['dirname'];
				
				if(!is_dir($baseDir));
					@mkdir($baseDir,0777,true);
			}
			
			echo 'Folders Created...<br />';

			for($i = 0; $i < $count; $i++) {
				$dest		=	str_replace($from,"",$installer['list'][$i]);
				$finalFile	=	$to.$dest;
				
				if(!is_file($installer['list'][$i]))
					continue;
					
				copy($installer['list'][$i],$finalFile);
				chmod($finalFile,0766);
			}
			
			echo 'Files Written...<br />';
		}
	}

function delete_folder_contents($dir)
	{
		$delEngine	=	new recursiveDelete();
		$delEngine->delete($dir);
	}

function clean_up_install($array = array())
	{
		if(empty($array))
			return;
		
		foreach($array as $dir)
			delete_folder_contents($dir);
		
		if(isset($_SESSION['install_key'])) {
			unset($_SESSION['install_key']);
		}
		
		if(isset($_SESSION['install'])) {
			unset($_SESSION['install']);
		}
	}

function fetchCreds($type = 'db')
	{	
		$dir	=	($type == 'db')? __DIR__.'/../../client_assets/settings/dbcreds.php' : __DIR__.'/../../client_assets/settings/api.php';
		$db		=	new FetchCreds();
		
		if(empty($db->_creds)) {
			$db	->setCreds($dir)
				->getCreds();
				
			$creds	=	$db->returnCreds();
			$creds	=	(!empty($creds))? Safe::to_array($creds) : false;
			
			if(!empty($creds)) {
				foreach($creds as $type => $val) {
					$new[$type]	=	base64_decode($val);
				}

				return (!empty($new))? $new : false;
			}
		}
	}

class	SaveDBCredentials extends DBCredentials
	{		
		public	function __construct($dir)
			{
				$this->install_dir	=	$dir;
			}
			
		public	function Create($setting = false,$compat = array())
			{
				// Make folder
				if(!empty($this->install_dir) && !is_dir($this->install_dir)) {
					if(!mkdir($this->install_dir, 0755, 1)) {
						global $_error;
						$_error['error']['creds']	=	'Failed: '.$_dir;
					}
				}
				// If folder is available, save credentials
				if(is_dir($this->install_dir)) {
					if(!empty($this->_string['api'])) {
						$apiFile = str_replace('//','/',$this->install_dir.'/api.php');
						if(is_file($apiFile)) {
							if(!unlink($apiFile))
								echo 'Could not delete API file...'.'<br />';
						}
						
						$this->WriteFileToDisk($this->_string['api'],'/api.php');
					}

					if(!empty($this->_string['db'])) {
						// Save files to disk
						if(is_file($dbFile = str_replace('//','/',$this->install_dir.'/dbcreds.php'))) {
							if(!unlink($dbFile))
								echo 'Could not delete API file...'.'<br />';
						}
						
						$this->WriteFileToDisk($this->_string['db'],'/dbcreds.php');
					}
				}
			}
	}

function saveCredentials($creds = false)
	{
		if(empty($creds))
			return false;
		
		if(!empty($creds['api']) && is_array($creds['api'])) {
			foreach($creds['api'] as $key => $value)
				$creds['api'][str_replace("n_","",$key)]	=	$value;
		}
		$SaveEngine	=	new SaveDBCredentials(__DIR__.'/../../client_assets/settings');

		if(!empty($creds['database']))
			$SaveEngine->CreateDB($creds['database']);

		if(!empty($creds['api']))
			$SaveEngine->CreateAPI($creds['api']);

		$SaveEngine->Create();
	}

function installSettings($content = false, $type = 'db')
	{
		// Default set as not new
		$new	=	false;
		// If the install version is set
		if(!empty($_SESSION['install']['version']))
			// Assign file date name
			$file	=	$_SESSION['install']['version'];
		else {
			// If not already set, assign new
			$new	=	true;
			// Assign the session as a date/time
			$_SESSION['install']['version']	=	date("YmdHis");
			// Assign $file the session version
			$file	=	$_SESSION['install']['version'];
		}
		// Save directory
		$dir	=	NBR_ROOT_DIR.'/setup';
		// Default save spot would be something like
		// /my/server/root/directory/webroot/installer/setup/20150101120134
		$nPref	=	$dir."/".$file;
		// If new or the content is not empty
		if($new || !empty($content)) {
			// Load the writer
			$nPrefs	=	$nPref."_{$type}.nprefs";
			AutoloadFunction("write_file");
			
			if(is_file($nPrefs))
				unlink($nPrefs);
			
			write_file(array("save_to"=>$nPrefs,"content"=>$content,"type"=>"a+"));
		}
		// Create a .htaccess file since we are making a sensitive document(s)
		if(!is_file($dir."/.htaccess")) {
			AutoloadFunction("CreateHTACCESS");
			CreateHTACCESS(array("dir"=>$dir,"rule"=>"server_rw"));
		}
	}

function getSettings($nPref = false)
	{
		if(is_file($nPref)) {
			$file	=	trim(file_get_contents($nPref));
			return json_decode($file,true);
		}
	}

function getSettingsList()
	{
		if(is_dir(NBR_ROOT_DIR.'/setup/')) {
			$filter		=	array('.htaccess','.','..');
			$settings	=	scandir(NBR_ROOT_DIR.'/setup/');
			$nSets		=	array_diff($settings,$filter);
			
			foreach($nSets as $dirs) {
				$type					=	(preg_match('/\_api/',$dirs))? 'api' : 'db';
				$instSettings[$type]	=	getSettings(NBR_ROOT_DIR.'/setup/'.$dirs);
			}
				
			return $instSettings;
		}
		
		return false;
	}

function parseCreds($array = false,$installed = false)
	{
		if(empty($array) || !is_array($array))
			return false;

		$cAPI['username']	=	'n_username';
		$cAPI['pin']		=	'n_pin';
		$cAPI['apikey']		=	'n_apikey';
		
		$cDB['user']		=	'username';
		$cDB['data']		=	'database';
		$cDB['pass']		=	'password';

		$compare			=	(isset($array['apikey']))? $cAPI : $cDB;

		foreach($array as $key => $value) {
			if($installed) {
				if(isset($compare[$key]))
					$key	=	$compare[$key];
			}

			$new[$key]	=	($installed)? $value : Safe::decOpenSSL($value,$_SESSION['install_key']);
		}

		return $new;
	}

function gatherCreds()
	{
		$fetchAPI			=	false;
		$fetchDB			=	false;
		// Fetch from settings
		$fetchCreds['api']	=	fetchCreds('api');
		$fetchCreds['db']	=	fetchCreds();
		// Fetched from stored
		$storedCreds		=	getSettingsList();
		
		if(!empty($fetchCreds['api']) && empty($storedCreds['api'])) {
			$fetchAPI	=	true;
			$api		=	$fetchCreds['api'];
		}
		elseif(empty($fetchCreds['api']) && !empty($storedCreds['api']))
			$api	=	$storedCreds['api'];
		elseif(!empty($fetchCreds['api']) && !empty($storedCreds['api']))
			$api	=	$storedCreds['api'];
		else
			$api	=	false;
		
		if(!empty($fetchCreds['db']) && empty($storedCreds['db'])) {
			$fetchDB	=	true;
			$db			=	$fetchCreds['db'];
		}
		elseif(empty($fetchCreds['db']) && !empty($storedCreds['db']))
			$db	=	$storedCreds['db'];
		elseif(!empty($fetchCreds['db']) && !empty($storedCreds['db']))
			$db	=	$storedCreds['db'];
		else
			$db	=	false;

		$creds['api']	=	(!empty($api))? parseCreds($api,$fetchAPI) : array();
		$creds['db']	=	(!empty($db))? parseCreds($db,$fetchDB) : array();

		return $creds;
	}

function normalizeCreds($creds)
	{
		$sessVer		=	(!empty($_SESSION['install']['version']))? $_SESSION['install']['version'] : false;
		$sessDir		=	NBR_ROOT_DIR.'/setup/';
		
		$creds['api']	=	(!empty($sessVer))? getSettings("{$sessDir}{$sessVer}_api.nprefs") : false;
		$creds['db']	=	(!empty($sessVer))? getSettings("{$sessDir}{$sessVer}_db.nprefs") : false;
		$creds['api']	=	(is_array($creds['api']))? $creds['api'] : array();
		$creds['db']	=	(is_array($creds['db']))? $creds['db'] : array();
		
		return $creds;
	}

function install_active()
	{
		return (!empty($_SESSION['install']) || !empty($_SESSION['reinstall']));
	}

error_reporting(E_ALL);
ini_set("display_errors",1);

gatherSession();
$creds	=	gatherCreds();