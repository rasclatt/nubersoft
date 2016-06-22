<?php
/*Title: create_client_config()*/
/*Description: This function writes the `config-client.php` file which is saved to the `/client_assets/settings/` folder.*/

	function create_client_config($settings = false,$keep = false)
		{
			
			AutoloadFunction('check_empty,FetchMixedId,directory_exists');
			
			$Writer	=	new WriteToFile();
			
			if(!empty($settings['settings'])) {
				foreach($settings['settings'] as $row) {
					if(!is_array($row))
						$instructions[]	=	$row;
				}
			}

			$savefolder			=	NBR_CLIENT_DIR.'/settings';
			if(!directory_exists($savefolder,array("make"=>true)))
				return false;
			
			$content['save_to']	=	$savefolder.'/config-client.php';
			$keep				=	(is_file($content['save_to']))? $keep:false;
			$content['type']	=	($keep != true)? 'a':'a+';
			$content['content']	=	'<?php';

			if(!$keep) {
					// Assign new opener
					$content['content']	.=	'
/*Title: client-config.php*/
/*Description: This file allows for cusomization on first-tier loading. This file is included after the standard `defines` on the config.php. This file can be edited manually or by calling the `create_client_config($options{array})` function. The `$options` would be any `php` script you would like in `array` format.

`$array = array("define('."'CONSTANT'".',$value);");
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
define("FILE_SALT","'.FetchMixedId(10,'salt').'");
// Max filesize for uploads
define("MAX_UP_FILES", 2000);
// Default contact
define("WEBMASTER","no-reply@".$_SERVER["HTTP_HOST"]);';
				}
			else {
				$file_copy		=	rtrim(rtrim(file_get_contents($content['save_to'])),"?>");
				$content['content']	.=	$file_copy;
			}
			
			$content['content']	.=	(isset($instructions))? "\r\n".implode("\r\n",$instructions):"";
			
			if(is_file($content['save_to']))
				unlink($content['save_to']);
			
			// Write to file
			$Writer->AddInput($content)->SaveDocument();
			return (is_file($content['save_to']))? true:false;
		}