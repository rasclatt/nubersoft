<?php
	class BuildDatabase
		{
			public		$reporting;
			
			protected	$_creds;
			
			private		$curr_path;
			private		$classes_path;
			private		$db_path;
			
			public	function __construct($_creds = array(), $classes_path = '/core.processor/', $db_path = '/dbconnect.php', $curr_path='/')
				{
					register_use(__METHOD__);
					
					$this->_creds		=	$_creds;
					// Current Directory
					$this->classes_path	=	$classes_path;
					$this->db_path		=	$db_path;
					$this->curr_path	=	$curr_path;
					global	$nubsql;
					
				//	$this->CreateCredsFile();
					
					if(!empty($_POST['username']) && !empty($_POST['password'])) {
						// Allow for inserting just user
						$sql	=	"insert ignore into users (unique_id, username, password, first_name) values('" . date('YmdHis') . rand(1000,9999) ."', '" . Safe::encode($_POST['username']) . "', '" . Safe::encode(hash('sha512', $_POST['password'])) . "', 'Admin')";
					}
					
					if(isset($dbhealth) && $dbhealth->rowCount() <= 1) {
						if(isset($_POST) && !empty($_POST)) {
							// Include the database guts
							if(isset($_POST['setup']) && $_POST['setup'] == 'run') {
								// Remote Connect to the nubersoft
								$getTables	=	new cURL('http://www.nubersoft.com/sqls/tables.php?action=magoo&type=soft');
								// Set response
								$response	=	$getTables->response;
								
								if(!empty($response)) {
								
									// Make/update all the tables
									$makeTable	=	new UpdateInstall($response); 

									if((is_dir($this->curr_path . '/') && $_REQUEST['consume'] == 'on') && (str_replace("/","",$this->curr_path) !== str_replace("/","",$_SERVER['DOCUMENT_ROOT']))) {
										header("Location: http://" . $_SERVER['SERVER_NAME']);
										include_once($this->curr_path._DS_.'classes'._DS_.'directory.engines'._DS_.'recursive.delete.php');
										$deleteQB	=	new recursiveDelete;
										$deleteQB->delete($this->curr_path._DS_);
										exit;
									}
								}
							else { ?>
							<h1>No response</h1>
							<?php }
							}
						}
					}
					elseif(isset($_POST['setup']) && $_POST['setup'] == 'insert_rows') {
							
							$inserts		=	$this->curr_path . '/scripts.installer/insert/sql.insert.php';
							include_once($inserts);
							
							$show_rows		=	$nubsql->fetch("show tables from $mysqlTable");
							
							$i = 0;
							foreach($show_rows as $insert)
								{
									$array_key	=	$insert['Tables_in_' . $mysqlTable];
									
									if(isset($_REQUEST[$array_key]) && $_REQUEST[$array_key][0] == 'on')
										{
											if(!isset($script[$array_key]['insert'])) { ?>
											<p style="font-size: 14px; margin: 8px 10px;"><span style="color: orange;">Table <?php echo ucwords(str_replace("_", " ", $array_key)); ?> does not have default content.</span></p><?php }
											else
												{
													if($_REQUEST[$array_key][1] == 'reset') {
															$delete_rows	=	$nubsql->Write("delete from $array_key");
														}
													
													$insert_rows	=	$nubsql->Write($script[$array_key]['insert']);
												}
										}
									elseif(isset($_REQUEST[$array_key]) && $_REQUEST[$array_key][0] !== 'on') { ?><p style="font-size: 14px; margin: 8px 10px;"><span style="color: orange;">Nothing was selected</span></p><?php 			}
								}
						}
					else {
							// Build all the tables
							if(check_empty($_POST,'setup','table_set')) {
									// Remote Connect to the nubersoft
									$getTables	=	new createConnection('http://www.nubersoft.com/sqls/tables.php?action=install');
									
									// Make/update all the tables
									$makeTable	=	new UpdateInstall($getTables);
								}
							
							// Create a user and password if none exists
							if(isset($_POST['gen_user']) && $_POST['gen_user'] == 'on') :
								if(isset($sql)):
									$create_tbls	=	new tableSetUp();
									$create_tbls->execute($sql, 'insert ignore', true);
								endif;
							endif;
						}
				}
		}