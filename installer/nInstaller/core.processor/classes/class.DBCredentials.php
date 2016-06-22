<?php
	class	DBCredentials
		{
			protected	$nubquery;
			protected	$credentials;
			protected	$install_dir;
			protected	$_string;
			protected	$_table;
			
			public	function __construct($install_dir = '/client_assets')
				{
					AutoloadFunction('nQuery');
					$this->install_dir	=	ROOT_DIR.$install_dir.'/settings';
					$this->nubquery		=	nQuery();
				}
				
			public	function CreateAPI($api = false)
				{
					// Check for passed parameters or forced
					if($api != false && is_array($api)) {
							$this->credentials['api']['apikey']		=	$api['apikey'];
							$this->credentials['api']['username']	=	$api['username'];
							$this->credentials['api']['pin']		=	$api['pin'];
						}
						
					// String to write
					$this->_string['api']	=	'<?php
	if(!function_exists("AutoloadFunction"))
		return;

	$this->_creds['."'pin'".']	=	"' . base64_encode($this->credentials['api']['pin']) . '";
	$this->_creds['."'apikey'".']	=	"' . base64_encode($this->credentials['api']['apikey']) . '";
	$this->_creds['."'username'".']	=	"' . base64_encode($this->credentials['api']['username']) . '";';
					
					return $this;
				}
				
			public	function CreateDB($dbcreds = false)
				{
					// Check for passed parameters or forced
					if($dbcreds != false && is_array($dbcreds)) {
							$this->credentials['db']['host']		=	$dbcreds['host'];
							$this->credentials['db']['database']	=	$dbcreds['database'];
							$this->credentials['db']['username']	=	$dbcreds['username'];
							$this->credentials['db']['password']	=	$dbcreds['password'];
						}
						
					// String to write
					$this->_string['db']	=	'<?php
	if(!function_exists("AutoloadFunction"))
		return;

	$this->_creds['."'user'".']	=	"' . base64_encode($this->credentials['db']['username']) . '";
	$this->_creds['."'pass'".']	=	"' . base64_encode($this->credentials['db']['password']) . '";
	$this->_creds['."'host'".']	=	"' . base64_encode($this->credentials['db']['host']) . '";
	$this->_creds['."'data'".']	=	"' . base64_encode($this->credentials['db']['database']) . '";';
					
					return $this;
				}

			public	function Create($dbcreds = array('host'=>false,'database'=>false,'username'=>false,'password'=>false),$api = array('apikey'=>false,'username'=>false))
				{
					if($dbcreds['host'] != false && $dbcreds['database'] != false && $dbcreds['username'] != false && $dbcreds['password'] != false && $api['apikey'] != false && $api['username'] != false) {
							// Set DB Credentials
							$this->credentials['db']['host']		=	$dbcreds['host'];
							$this->credentials['db']['database']	=	$dbcreds['database'];
							$this->credentials['db']['username']	=	$dbcreds['username'];
							$this->credentials['db']['password']	=	$dbcreds['password'];
							// Set API credentials
							$this->credentials['api']['apikey']		=	$api['apikey'];
							$this->credentials['api']['username']	=	$api['username'];
						}
					else
						return $this;
					
					// Make folder
					if(!is_dir($this->install_dir)) {
							if(!mkdir($this->install_dir, 0755, 1)) {
									global $_error;
									$_error['error']['creds']	=	'Failed: '.$_dir;
								}
						}
						
					// If folder is available, save credentials
					if(is_dir($this->install_dir)) {
							// Set up API Credentials
							$this->CreateAPI();
							// Set up DB Credentials
							$this->CreateDB();
							// Save files to disk
							if(is_file($this->install_dir.'/api.php'))
								unlink($this->install_dir.'/api.php');
							$this->WriteFileToDisk($this->_string['api'],'/api.php');
							// Save files to disk
							if(is_file($this->install_dir.'/dbcreds.php'))
								unlink($this->install_dir.'/dbcreds.php');
							$this->WriteFileToDisk($this->_string['db'],'/dbcreds.php');
						}
					
					return $this;
				}
			
			public	function WriteFileToDisk($data = false, $name = '/api.php')
				{
					try {
						// Check that folder exists
						if(is_dir($this->install_dir)) {
							$write	=	new WriteToFile();
							// Save API file to disk
							$write	->AddInput(array("save_to"=>str_replace("//","/",$this->install_dir."/".$name),"content"=>$data))
									->SaveDocument();
						}
					} catch (Exception $e) {
						if(is_admin())
							echo printpre($e);
					}
					
					return $this;
				}
				
			protected	function Connect()
				{
					$this->_table	=	(!isset($this->_table))? 'all':$this->_table;
					$_url	=	"http://www.nubersoft.com/api/index.php?service=Fetch.Table&table=".urlencode($this->_table);
					$json	=	new cURL();
					$response	=	$json->Connect($_url);
					return	$response;
				}
			
			public	function InstallDBTables()
				{
					AutoloadFunction('check_dbconnection,nuber_faux');
					
					// Get the db credentials
					$credentials	=	new FetchCreds();
					$database		=	base64_decode($credentials->_creds['data']);
					$response		=	$this->Connect();
					
					if(!empty($response['table_name'])) {
							foreach($response['table_name'] as $table) {
									$tbl	=	$this->nubquery->tableExists($table,false,true)->results;
									
									if($tbl == 1) {
											if($table != 'users')
												// Alter table names
												$this->nubquery->addCustom("rename table ".$table." to nb".date("YmdHis")."_".$table,true)->write();
										}
								}
							$rTableCnt	=	count($response['table']);
							for($i = 0; $i < $rTableCnt; $i++) {
									$this->nubquery->addCustom($response['table'][$i],true)->write();
								}
							
							foreach($response['rows'] as $inserts) {
									if($inserts['table'] != 'main_menus')
										$this->nubquery->addCustom("insert into `".$inserts['table']."` (".$inserts['keys'].") values ".implode(",",$inserts['values']),true)->write();
									else {
											AutoloadFunction('install_default_menus');
											install_default_menus();
										}
								}
						}
						
				}
			
			public	function Install($_table = 'all')
				{
					$this->_table			=	$_table;
					$_tableOpts				=	$this->Connect();

					$_tableSQL['create']	=	$_tableOpts['table'];
					$_tableSQL['names']		=	$_tableOpts['table_name'];
					$_tableSQL['rows']		=	$_tableOpts['rows'];
		
					if(!empty($_tableSQL['create']) && $this->nubquery != false) {
							
							foreach($_tableSQL['create'] as $table_id => $statements) {
									
									$this->nubquery->addCustom($statements,true)->write();
									
									$_tbl_name	=	$_tableOpts['table_name'][$table_id];
									$_insert	=	$this->nubquery->addCustom("describe ".$_tbl_name,true);
									
									foreach($_insert as $_fields) {
											$_cols_inTbl[$_tbl_name][]	=	$_fields['Field'];
										}
									
									$search		=	array_search($_tbl_name,$_tableOpts['table_name']);
									
									if(isset($_tableOpts['rows'][$search])) {
											$this->nubquery->addCustom("delete from `".$_tableOpts['table_name'][$table_id]."` where core_setting = '1'",true)->write();
											$_hdRow	=	$_tableOpts['rows'][$search]['keys'];
											$_Rows	=	implode(",",$_tableOpts['rows'][$search]['values']);
											$this->nubquery->addCustom("insert ignore into ".$_tableOpts['table_name'][$table_id]." ($_hdRow) values \r\n $_Rows",true)-write();
										}
									
								//	$nubsql->fetch("drop table ".$_tbl_name);
									
									/*
									if(isset($_tableOpts['table_rows'][$table_id])) {
											foreach($_tableOpts['table_rows'][$table_id] as $FieldsId => $ColArray) {
													
													$_ID	=	$FieldsId;
													$_str	=	$_tableOpts['table_rows'][$table_id][$FieldsId]['string'];
													
													if(!in_array($_ID,$_cols_inTbl)) {
															// Add any columns that need
															$nubsql->Write($_str);
														}
													
													if(isset($_tableOpts['rows'][$table_id]['values'])) {
															$_hdRow	=	$_tableOpts['rows'][$table_id]['keys'];
															$_Rows	=	implode(",",$_tableOpts['rows'][$table_id]['values']);
															$nubsql->Write("insert ignore into ".$_tableOpts['table_name'][$table_id]." ($_hdRow) values \r\n $_Rows");
														}
												}
										}
										
									*/
								}
								
						//	exit;
						}
					
					// Send back false (used mainly for check in core file)
					return false;
				}
			
			public	function saveCredentials()
				{
					// Make folder
					if(!is_dir($this->install_dir)) {
							if(!mkdir($this->install_dir, 0755, 1)) {
									return $this;
								}
						}
						
					// If folder is available, save credentials
					if(is_dir($this->install_dir)) {
							// Save files to disk
							if(is_file($this->install_dir.'/dbcreds.php'))
								unlink($this->install_dir.'/dbcreds.php');
							
							$this->WriteFileToDisk($this->_string['db'],'/dbcreds.php');
						}
					
					return $this;
				}
		}