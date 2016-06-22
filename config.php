<?php
// Define important directories
require_once(__DIR__.DIRECTORY_SEPARATOR.'defines.php');
//	Include Function Autoloader
require_once(NBR_FUNCTIONS._DS_.'function.AutoloadFunction.php');
// Use autoloader to load core functions and class autoloader
autoloadFunction('autoload_core_functions,nLoader');
// Autoload classes
spl_autoload_register('nLoader');
// Allow session to start
if((!defined('SESSION_ON')) || (defined('SESSION_ON') && SESSION_ON)) {
	if(!isset($_SESSION))
		\nApp::nSession()->start();
}
//	Default functions folder
//	Auto Load Functions
\Nubersoft\nObserverProcessor::loadCoreFunctions();
//	Load client config file if available
\Nubersoft\nObserverProcessor::loadClientConfig();
// Make sure there are default defines for usergroups
// Top level usergroup
if(!defined("NBR_SUPERUSER"))
	define("NBR_SUPERUSER",(int) 1);
// 2nd Tier usergroup
if(!defined("NBR_ADMIN"))
	define("NBR_ADMIN", (int) 2);
// Average webuser
if(!defined("NBR_WEB"))
	define("NBR_WEB", (int) 3);

// ERROR SUPPRESSION: Set to false unless in test
$display_errors	=	error_check(); //ini_set("display_errors",1); error_reporting(E_ALL);
// File salting. !****OVERRIDE THESE IN YOUR REGISTRY FILE-> /client_assets/settings/registry.xml
\Nubersoft\nObserverProcessor::setPresets();
// Fetch config and add list if there is one cached
\Nubersoft\nObserverProcessor::getCachedPrefs(\nApp::getSite('cache_folder'));
// Add in more defines
\nApp::autoAddDefines();
//	Fetch client_assets functions
load_clientfunctions();
// Header options
// Defense against ClickJacking hack: Allows only iframes from this site
header('X-Frame-Options: SAMEORIGIN');
// Allow html submission
if(is_admin())
	header("X-XSS-Protection: 0");
// Assign filter values to request arrays
format_input();
//*******************************************//
//******** PASSWORD ENCRYPTION **************//
//*******************************************//
// Accepts PasswordGenerator::BCRYPT
// Default: PasswordGenerator::PASS_HASH
// The function will switch to bcrypt/blowfish if password_hash is not available
PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
// If user settings are present
// Change database configuration in the db/creds.php file
if(is_file($database_credentials = NBR_CLIENT_DIR._DS_.'settings'._DS_.'dbcreds.php')) {
	// Create OverLoaded Settings
	nuber_faux($settings);
	// Initiate database connection: Credientals for MySQL, Server Status, Error Handling Toggle
	DatabaseConfig::connect();
	// Create static settings for site prefs
	nApp::setSystemSettings();
	// Store Database Name
	nApp::saveSetting('engine',array('dbname'=>nApp::getDbName()));
	// Send verification that server is working
	nApp::saveSetting('engine',array('sql'=>nApp::siteValid()));
	// If live status has not yet been determined by now, set it to offline
	nApp::saveSetting('engine',array('site_live'=>nApp::siteLive()));
	// Save table data as name and numeric
	if(nApp::siteValid()) {
		$adminTable = nApp::getDefaultTable();
		nApp::saveSetting('table_name',fetch_table_name($adminTable));
		nApp::saveSetting('table_id',fetch_table_id($adminTable));
		nApp::saveSetting('page_prefs',nApp::getPage());
		nApp::saveSetting('bypass',nApp::getBypass());
		// See if table exists
		// Assign default if false
		if(nApp::tableValid($adminTable)) {
			nApp::saveSetting('engine',array("table"=>$adminTable));
		}
		else {
			// Set default tables
			nApp::saveSetting('engine',array(
				'table_name'=>'users',
				'table_id'=>fetch_table_id(nApp::getTableName()),
				'tables'=>fetch_table_id(nApp::getTableName())
				)
			);
		}
	}
	else
		nApp::saveSetting('engine',array(
			'table_name'=>false,
			'table_id'=>false,
			'tables'=>false
			)
		);
}
else {
	$con = $nubsql = $nubquery = false;
	nApp::saveSetting("engine",false);
}
// Set timezone
date_default_timezone_set(get_timezone());
// Check session expiration
$session_expire	=	nApp::getSessExpTime();
ValidateSession::Check($session_expire);
// Save the session expire to NubeData
nApp::saveSetting('session_expire',$session_expire);