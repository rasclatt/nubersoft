<?php
	function load_client_config($break = false)
		{
			register_use(__FUNCTION__);
			$client_config = CLIENT_DIR.'/settings/config-client.php';
			// This will check if there is a reset command set
			AutoloadFunction('check_empty');
			if(check_empty($_GET,'command','client_config')) {
					// If there is and the user is an admin
					AutoloadFunction('is_admin');
					// Start session for reset purposes
					session_start();
					if(is_admin()) {
							// Try and create a file
							AutoloadFunction('create_client_config');
							create_client_config();
							$try	=	true;
						}
				}
			
			if(is_file($client_config)) {
					include_once($client_config);
					
					if(isset($try) && $try == true) {
							AutoloadFunction('is_admin');
							if(is_admin()) {
									AutoloadFunction('register_global_error');
									register_global_error("client_config","Client Config Added");
								}
						}
						
					return true;
				}
			else {
					// Return false if this effort has alredy been tried before.
					if($break == true) {
							AutoloadFunction('is_admin');
							session_start();
							if(is_admin()) {
									AutoloadFunction('register_global_error');
									register_global_error("client_config",'Failed to load client config.');
								}
								
							return false;
						}

					// Try and create a file
					AutoloadFunction('create_client_config');
					create_client_config();
					// Try loading page again, set break this time
					load_client_config(true);
				}
		}