<?php
	
	class	DBWriter
		{
			protected	$nuber;
			protected	$payload;
			protected	$cols_in_table;
			protected	$PasswordEngine;
			
			public	function __construct()
				{
					$this->PasswordEngine	=	new CreateUser();
				}
			
			protected	function rewrite_htaccess()
				{
					if(!empty($this->payload["requestTable"])) {
						if($this->payload["requestTable"] == 'system_settings' && !empty($this->payload['content']['htaccess'])) {
							AutoloadFunction("get_default_htaccess");
							get_default_htaccess(array("htaccess"=>Safe::decode($this->payload['content']['htaccess']),"write"=>true));
						}
					}
				}
			
			public	function execute($payload = false)
				{
					AutoloadFunction('is_admin,check_empty,call_action,compare,nQuery');
					$nubquery		=	nQuery();
					$this->payload	=	(is_array($payload))? $payload:$_POST;
					
					NubeData::$settings->engine->table_name	=	(!empty($this->payload['requestTable']))? $this->payload['requestTable']: NubeData::$settings->engine->table_name;
					
					$_table					=	NubeData::$settings->engine->table_name;
					// Get table columns
					$this->cols_in_table	=	$nubquery->tableExists(NubeData::$settings->engine->table_name);
					// Create / Update password
					$this->payload			=	$this->PasswordEngine	->check($this->payload)
																		->execute(true)
																		->payload;	
					if(call_action('add',$this->payload)) {
						if(isset($this->payload['ID']))
							unset($this->payload['ID']);
					}
						
					// Do an htaccess rewrite on system_settings
					$this->rewrite_htaccess();
					
					$_checkAction[]		=	(isset($_REQUEST['add']) || isset($this->payload['add']))? 1:0;
					$_checkAction[]		=	(isset($_REQUEST['update']) || isset($this->payload['update']))? 1:0;
					
					// Add a unique id
					if(call_action('add',$this->payload)) {
						AutoloadFunction('FetchUniqueId');
						$this->payload['unique_id']	=	(empty($this->payload['unique_id']))? FetchUniqueId(rand(1000,9999)):$this->payload['unique_id'];
					}
					// Go at it provided all is well...
					if(($nubquery != false) && in_array(1,$_checkAction)) {
						// If requestTable is set and usergroup permitted
						if(!empty($_table)) {// && array_sum($_allow) >= 1
							if(check_empty($this->payload,'delete','on')) {
								// See if image is associated with row
								$checkImage	=	$nubquery	->select()
															->from($_table)
															->where(array("ID" =>$this->payload['ID']))
															->fetch();
								if($checkImage != 0) {
									if(isset($checkImage[0]['file_name']) && isset($checkImage[0]['file_name'])) {
										AutoloadFunction('delete_upload');
										delete_upload(array("file_name"=>$checkImage[0]['file_name'],"file_path"=>$checkImage[0]['file_path']));
										if(defined("THUMB_DIR")) {
											$thumbnail	=	new ImageFactory();
											$thumbnail	->SearchLocation(THUMB_DIR)
														->SearchFor(THUMB_DIR."/".$checkImage[0]['file_name'])
														->ScrapThumbnails();
										}
									}
								}
							}
							
							// Upload files and return files array
							AutoloadFunction('upload_file');
							$naming		=	(check_empty($this->payload,'keep_name','1'))? array("name"=>1,"payload"=>$this->payload,"table"=>$_table):array("payload"=>$this->payload,"table"=>$_table);
							$uploads	=	upload_file($naming);
						}
					}
					
					// Process Tables that have file columns
					if(isset($uploads) && $uploads != false) {
						// See if action is add
						if(call_action('add',$this->payload)) {
							// See if the count is greater than 1
							if(compare($uploads['count'],1,">") && is_array($uploads['files'])) {
								foreach($uploads['files'] as $key => $files_to_add) {
									$this->WriteFileInstance($files_to_add);
								}
							}
							elseif(compare($uploads['count'],1,"="))
								$this->WriteFileInstance($uploads['files'][0]);
						}
						else
							$this->WriteFileInstance($uploads['files'][0],'update');
						
						// Stop the process from continuing
						return;
					}
					// Action is to add into db
					if(call_action('add',$this->payload)) {
						$this->WriteFileInstance($this->payload,'insert');
					}
					// Action is to update row
					elseif(call_action('update',$this->payload)) {
						// Check if delete is on
						if(check_empty($this->payload,'delete','on') && !empty($this->payload['ID'])) {
							$nubquery	->delete()
										->from(NubeData::$settings->engine->table_name)
										->where(array("ID"=>$this->payload['ID']))
										->write();
						}
						else {
							if(!empty($this->payload['ID']))
								$this->WriteFileInstance($this->payload,'update');
						}
					}
				}
				
			protected	function WriteFileInstance($files_to_add = false,$type = 'insert')
				{ 
					if(!is_array($files_to_add))
						return;
						
					AutoloadFunction('filter_action_words,combine_arrays,compare_post_data,check_empty,call_action,nQuery');
					
					// Create new query for sake of ease
					$query		=	nQuery();
					// Assign Table
					$table		=	NubeData::$settings->engine->table_name;
					if(check_empty($this->payload,'override','1'))
						$allowEmpty	=	true;
					elseif(is_admin())
						$allowEmpty	=	true;
					else
						$allowEmpty	=	false;
					// This is for writing to logs
					$author		=	(!empty($_SESSION['username']))? $_SESSION['username']:$_SERVER['REMOTE_ADDR'];
					
					if(isset($this->payload['override']))
						unset($this->payload['override']);
					
					// Assign a unique_id if not already set
					if(empty($this->payload["unique_id"])) {
						AutoloadFunction('FetchUniqueId');
						$files_to_add["unique_id"]	=	FetchUniqueId(rand(1000,9999));
					}

					// Get columns from table
					if(!isset($this->cols_in_table))
						$this->cols_in_table	=	$query->tableExists($table);
						
					// Filter out all resvered keys from post array
					$filter_post	=	array_diff_key($this->payload,filter_action_words('key'));
					// Filter out all resvered keys from file array
					$files_to_add	=	array_diff_key($files_to_add,filter_action_words('key'));
					// Combine all arrays and filter out empty
					$final			=	combine_arrays($files_to_add,$filter_post,$allowEmpty);
					// Filter out empty columns
					//$cols			=	compare_post_data($this->cols_in_table->columns_in_table,$final);
					// Insert into table
					AutoloadFunction("QuickWrite");
					if($type == 'insert') {
						$query	->insert($table)
								->setColumns(array_keys($final))
								->setValues(array($final))
								->write();
						
						// Write sql disk
						if(!empty($query->statement))
							$log	=	array("data"=>"sql_write: Authored by ".$author." / ".$query->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>CLIENT_DIR."/settings/error_log/","filename"=>"sql.write.txt","mode"=>"c+");
					}
					else {
						// If delete, delete
						if(check_empty($this->payload,'delete','on')) {
							$query	->delete()
									->from($table)
									->where(array("ID"=>$this->payload['ID']))
									->write();// Write sql disk

							if(!empty($query->statement))
								$log	=	array("data"=>"sql_delete: By ".$author." / ".$query->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>CLIENT_DIR."/settings/error_log/","filename"=>"sql.delete.txt","mode"=>"c+");
						}
						else {
							// Match $_POST/$_FILES against table
							$vals	=	$query->tableExists($table,$final)->matched;
							
							// Update
							$query	->update($table)
									->set($vals)
									->where(array("ID"=>$this->payload['ID']))
									->write();
							
							if(!empty($query->statement))
								$log	=	array("data"=>"sql_update: Authored by ".$author." / ".$query->statement."/","dir"=>CLIENT_DIR."/settings/error_log/","filename"=>"sql.update.txt","mode"=>"c+");
						}
					}
						
					if(empty($query->err['sql']) && is_admin()) {
						AutoloadFunction('render_generic_page');
						render_generic_page(array("content"=>'<div style="background-color: #FFF;"><h2 class="nbshclass">SQL Error:</h2><div class="nbsgenlayer">'.printpre($query->err,__LINE__,__FILE__).PHP_EOL.PHP_EOL.printpre($query).PHP_EOL.PHP_EOL.printpre($this->payload,"<h1>PAYLOAD</h1>").'</div></div>',"exit"=>true,"back"=>$_SERVER['HTTP_REFERER']));
						exit;
					}
						
					// Write to a log
					if(!empty($log))
						QuickWrite($log);
					
					return $this;
				}
		}