<?php
// Allow session to start
if((!defined('SESSION_ON')) || (defined('SESSION_ON') && SESSION_ON)) {
	if(!isset($_SESSION))
		session_start();
}
// Define important directories
define("NBR_ROOT_DIR", __DIR__);
define('NBR_CORE', NBR_ROOT_DIR.'/core');
define("NBR_CLASS_CORE", NBR_ROOT_DIR."/core/classes");
define("NBR_PLUGINS", NBR_ROOT_DIR."/plugins");
define("NBR_CLIENT_DIR", NBR_ROOT_DIR."/client_assets");
define("NBR_RENDER_LIB", NBR_ROOT_DIR."/core/renderlib");
define("NBR_TEMPLATE_DIR",NBR_ROOT_DIR."/core/template");
define("NBR_FUNCTIONS",NBR_ROOT_DIR."/core/functions");
define("NBR_ENGINE_CORE",NBR_ROOT_DIR."/core/engine");
define("NBR_ENGINE_CLIENT",NBR_CLIENT_DIR."/settings/engine");
define("NBR_THUMB_DIR",NBR_CLIENT_DIR."/thumbs");
define("NBR_AJAX_DIR",NBR_ROOT_DIR."/ajax");
//	Include Function Autoloader
include(NBR_FUNCTIONS.'/function.AutoloadFunction.php');
// Use autoloader to load core functions and class autoloader
AutoloadFunction('autoload_core_functions,nLoader');
// Autoload classes
spl_autoload_register('nLoader');
//	Default functions folder
//	Auto Load Functions
autoload_core_functions();
//	Load client config file if available
load_client_config();
//	Fetch client_assets functions
load_clientfunctions();
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
// Header options
// Defense against ClickJacking hack: Allows only iframes from this site
header('X-Frame-Options: SAMEORIGIN');
// Allow html submission
if(is_admin())
	header("X-XSS-Protection: 0");
// Assign filter values to request arrays
format_input();
// ERROR SUPPRESSION: Set to false unless in test
$display_errors	=	error_check();
// File salting. !****OVERRIDE THESE IN YOUR REGISTRY FILE-> /client_assets/settings/registry.xml
$settings['engine']['openssl_salt']		=	(defined("OPENSSL_SALT"))? OPENSSL_SALT: "1029374537280172";
$settings['engine']['openssl_iv']		=	(defined("OPENSSL_IV"))? OPENSSL_IV: "0192472903847283";
$settings['engine']['file_salt']		=	(defined("FILE_SALT"))? FILE_SALT: "saltstash";
// TEMPLATE: Error page layout (requires full file path)
$settings['site']['error_404']			=	NBR_TEMPLATE_DIR."/default/site.error404.php";
// Default 
$settings['site']['template_folder']	=	NBR_TEMPLATE_DIR."/default/";
// Default header
$settings['site']['template_head']		=	NBR_TEMPLATE_DIR."/default/";
// Layout for the prefs page template
$settings['site']['system_prefs']		=	(!defined("SYS_PREFS_TEMP") || (defined("SYS_PREFS_TEMP") && !is_file(SYS_PREFS_TEMP)))? "/ajax/form.site.prefs.php":SYS_PREFS_TEMP;
// Save folder for tempfiles
$settings['site']['temp_folder']		=	(!defined("TEMP_DIR"))? NBR_ROOT_DIR.'/../temp/':NBR_ROOT_DIR.TEMP_DIR;
// Save cache folder
$settings['site']['cache_folder']		=	(!defined("CACHE_DIR"))? NBR_ROOT_DIR.'/../cache/':NBR_ROOT_DIR.CACHE_DIR;
// This is overwritten on latter template retrieval. This is just default
$settings['site']['template']			=	'default/template';
//*******************************************//
//******** PASSWORD ENCRYPTION **************//
//*******************************************//
// Accepts PasswordGenerator::BCRYPT
// Default: PasswordGenerator::PASS_HASH
// The function will switch to bcrypt/blowfish if password_hash is not available
PasswordGenerator::Engine(PasswordGenerator::USE_DEFAULT);
// If user settings are present
// Change database configuration in the db/creds.php file
if(is_file($database_credentials = NBR_CLIENT_DIR.'/settings/dbcreds.php')) {
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
			nApp::saveSetting('engine',array(	'table_name'=>'users',
												'table_id'=>fetch_table_id(nApp::getTableName()),
												'tables'=>fetch_table_id(nApp::getTableName())));

		}
	}
	else
		nApp::saveSetting('engine',array('table_name'=>false,'table_id'=>false,'tables'=>false));
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
