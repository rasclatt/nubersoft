<?php
	class	DeleteCache
		{
			const	SUPERUSER	=	1;
			const	ADMIN		=	2;
			const	DECODE_DIR	=	true;
			const	KEEP_DIR	=	false;
			const	SUPRESS_ERR	=	true;
			
			private	static	$dir;
			
			public	static	function execute($dir)
				{
					self::$dir	=	$dir;
					AutoloadFunction('is_admin,check_empty');
					if(is_dir(self::$dir)) {
						if(check_empty($_POST,'command','cache.flush') || check_empty($_REQUEST,'auto_cache','on')) {
							if(!isset($_SESSION['usergroup']))
								session_start();
								
							if(is_admin()) { 
								if(check_empty($_REQUEST,'auto_cache','on')) {
									session_start();
									$deleteCache	=	new	\recursiveDelete;
									$deleteCache->delete(self::$dir."/$_REQUEST[unique_id]");
								}
								elseif(check_empty($_REQUEST,'command','cache.flush')){
									session_start();
									if(is_admin()) {
										$deleteCache	=	new	\recursiveDelete;
										$deleteCache->delete(self::$dir);
											
										$redirect		=	new \browser_redirectQS($_REQUEST);
										header("Location:" .  $redirect->returnFinal);
									}
									else
										header("Location: http://" . $_SERVER['HTTP_HOST']);
								}
							}
						}
					}
					else {
						$redirect		=	new browser_redirectQS($_REQUEST);
						header("Location:" .  $redirect->returnFinal);
					}
				}
				
			// This one is a general delete.
			// Directions:
			// 1) Choose the directory to remove
			// 2) If Admin equals or less than chosen group, allow action
			// 3) If decode set to true, then the assumption is that it will receive a base64 encoded url
			// 4) If supress true, then don't echo anything, return the response array and send error to global
			public	static	function Delete($dir,$admin = 2,$decode = true,$supressEcho = false)
				{
					$_allowed	=	is_admin($admin);	
					$dir		=	($decode)? base64_decode($dir) : $dir;
					$register	=	new \RegisterSetting();
					try {
						if($_allowed && is_dir($dir)) {
							$deleteCache	=	new	\recursiveDelete;
							$deleteCache->delete($dir);
							if(!$supressEcho) {
								echo "Recursive Directory Deleted: $dir";
								$register->UseData('cache',true)->SaveTo('errors');
							}
							else {
								$register->UseData('cache',true)->SaveTo('errors');
							}
						}
						else {
							if(!$_allowed) {
								$err		=	'perm';
								$_message	=	'Permission denied.';
							}
							elseif(!is_dir($dir)){
								$err		=	'empty';
								$_message	=	'Folder removed/No folder exists.';
							}
							else {
								$err		=	'invalid';
								$_message	=	'Unknown Error.';
							}
							
							if($supressEcho == false) {
								echo $_message;
							}
							else {				
								global $_error;
								$_error['cache']['delete'][]	=	$_message;
							}
							
							$register->UseData('cache',array($err=>$_message))->SaveTo('errors');
						}
					}
					catch(\Exception $e) {
						if(is_admin())
							die($e->getMessage());
					}
					
					if(isset($_error) && !$supressEcho)
						return $_error;
				}
			
		}