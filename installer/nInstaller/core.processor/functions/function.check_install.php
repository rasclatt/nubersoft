<?php
	function check_install()
		{
			register_use(__FUNCTION__);
			AutoloadFunction('check_empty,ValidateToken');
			$installit['files']		=	(!empty($_POST['token']) && (isset($_POST['token']['dbinstall']) && ValidateToken('dbinstall',$_POST['token']['dbinstall'])))? 1:0;
			$installit['data']		=	(!empty($_POST['token']) && (isset($_POST['token']['insertdata']) && ValidateToken('insertdata',$_POST['token']['insertdata'])))? 1:0;
			$installit['reinstall']	=	(!empty($_GET['reinstall']) && ValidateToken('reinstall',$_GET['reinstall']))? 1:0;
			$installit['build_site']=	(check_empty($_POST,'build_site','true'))? 1:0;
			
			if($installit['reinstall'] == 1)
				$force_install	=	true;
			
			if(array_sum($installit) > 0)
				// New creds engine
				$CredEngine				=	new DBCredentials();

			if($installit['build_site'] == 1) {
					$valid				=	(ValidateToken('insertdata',$_POST['token']['insertdata']))? true:false;
					$CredEngine->InstallDBTables();
					$installit['data']	=	0;
				}
				
			if($installit['data'] == 1) {
					$CredEngine->Install('all');  ?>
		<script>
			$(document).ready(function() {
					window.location	=	'';
				});
		</script><?php	
				}
			elseif($installit['files'] == 1) {
					AutoloadFunction('check_dbconnection');
					// Filter credentials
					$dbusername	=	preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($_POST['dbusername']));
					$dbpassword	=	preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($_POST['dbpassword']));
					$dbhost		=	preg_replace('/[^0-9a-zA-Z\-\_\.]/','',trim($_POST['host']));
					$dbdatabase	=	preg_replace('/[^0-9a-zA-Z\-\_\.]/','',trim($_POST['database']));
					// Filter API credentials
					$apikey		=	preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($_POST['api_key']));
					$apiuser	=	preg_replace('/[^0-9a-zA-Z\-\_]/','',trim($_POST['api_username']));
					
					// Check that database exists
					$e			=	check_dbconnection(array("dbdatabase"=>$dbdatabase,"dbpassword"=>$dbpassword,"host"=>$dbhost,"database"=>$dbdatabase));
						
					// Save credentials to disk
					if(!isset($e)) {
							// Save database
							$dbcreds['host']		=	$dbhost;
							$dbcreds['database']	=	$dbdatabase;
							$dbcreds['username']	=	$dbusername;
							$dbcreds['password']	=	$dbpassword;
							// Save api
							$api['apikey']			=	$apikey;
							$api['username']		=	$apiuser;
							// Process
							$CredEngine->Create($dbcreds,$api);
						}
				}
			
			return (isset($force_install))? $force_install:false;
		}
?>