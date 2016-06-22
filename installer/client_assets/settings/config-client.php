<?php
/*Title: client-config.php*/
/*Description: This file allows for cusomization on first-tier loading. This file is included after the standard `defines` on the config.php. This file can be edited manually or by calling the `create_client_config($options{array})` function. The `$options` would be any `php` script you would like in `array` format.

`$array = array("define('CONSTANT',$value);");
create_client_config($array);`
 */

// Database Kind
define("DBENGINE","MYSQL");
// Turn on error reporting
define("SERVER_MODE", true);
// Turn on sessioning
define("SESSION_ON",true);
// Allow All Functions
define("F_ACCESS",true);
// Do not use this for permanent salt storage
// incase the salt is updated 
define("FILE_SALT","saltQ712wHViZs");
// Max filesize for uploads
define("MAX_UP_FILES", 2000);
// Default contact
define("WEBMASTER","no-reply@".$_SERVER["HTTP_HOST"]);