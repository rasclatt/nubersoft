<?php
/*Title: ConstructMySQL*/
/*Description: This function queries the MySQL database*/
/**
* nUberSoft
*
* NOTICE OF LICENSE
* This source file is subject to the Open Software License (OSL 3.0)
* @copyright  Copyright (c) 2014 Ryan Rayner / nUberSoft (http://www.nUberSoft.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* 
* NOTE: CLASS IS AN UNFINISHED BETA. Use at your own risk.
*/

	class ConstructMySQL implements QueryEngine
		{
			public		$errors;
			public		$err;
			public		$rowCount;
			public		$results;
			public		$sql_bind;
			public		$columns_in_table;
			public		$publishable;
			public		$table;
			public		$columns;
			public		$database;
			public		$data;
			public		$table_cols;
			public		$column_layout;
			public		$recordata;
			public		$matched;
			
			protected	$valid_table;
			protected	$sql;
			protected	$bind;
			protected	$sql_where;
			protected	$i;
			protected	$set_limit;
			protected	$orderby;
			protected	$unserial;
			protected	$serial;
			protected	$query;
			protected	$result;
			
			//protected	static	$valid_tables;
			protected	static	$singleton;
			
			private		$statement;
			private		$DatabaseConfig;
			
			const		FETCH_OBJECT	=	'obj';
			const		TEST_MODE		=	'test';
			const		ALLOW_EMPTY		=	true;
			const		NEW_ENGINE		=	false;

			public	function __construct($dbObject = false)
				{
					$this->DatabaseConfig	=	$dbObject;
					
					if(empty(self::$singleton)) {
						self::$singleton		=	$this;
					}
					
					return self::$singleton;
				}
			
			public	function useConnection($dbObject)
				{
					$this->DatabaseConfig	=	$dbObject;
					
					return $this;
				}
			
			protected	function resetStored()
				{
					register_use(__METHOD__);
					
					$this->sql			=	array();
					$this->err			=	array();
					$this->bind			=	false;
					$this->rowCount		=	0;	
					$this->orderby		=	'';	
					$this->sql_bind		=	false;
					$this->sql_where	=	false;
					$this->statement	=	"";
					$this->results		=	0;
					$this->set_limit	=	"";
					$this->orderby		=	"";
					$this->columns		=	false;
					$this->publishable	=	false;
					$this->table_cols	=	false;
					$this->unserial		=	false;
					$this->valid_table	=	true;
					$this->table		=	false;
				}
			
			public	function select($values = array(),$distinct = false)
				{
					register_use(__METHOD__);
					// Reset all values
					$this->resetStored();
					if(is_array($values))
						$values		=	(!empty($values))? "`".implode("`, `",$values)."`":"*";
					
					$this->sql[]	=	"SELECT";
					
					if($distinct)
						$this->sql[]	=	"DISTINCT";
						
					$this->sql[]	=	$values;
					
					$this->execute();
					return $this;
				}
			
			public	function selectCount($settings = false)
				{
					$title		=	(!empty($settings['title']))? $settings['title'] : "count";
					$values		=	(!empty($settings['values']))? $settings['values'] : "*";
					$distinct	=	(!empty($settings['distinct']))? "DISTINCT " : "";
					
					if(is_array($settings['values']))
						$values	=	"`".implode("`, `",$settings['values'])."`";
					
					$this->select("COUNT({$distinct}{$values}) as {$title}");
				}
			
			public	function delete()
				{
					register_use(__METHOD__);
					// Reset all values
					$this->resetStored();
					if(!in_array("DELETE",$this->sql))
						$this->sql[]	=	"DELETE";
						
					$this->execute();
					return $this;
				}
			
			public	function from($values = false,$forceValid = false)
				{
					register_use(__METHOD__);
					
					$this->valid_table	=	true;
					if(is_array($this->sql) && !in_array("FROM",$this->sql))
						$this->sql[]		=	"FROM";
	
					if(!is_array($values)) {	
							$this->valid_table	=	$this->checkValidTables($values);
							// Force the sql to run with a table that has not been verified
							if($forceValid) {
								$this->valid_table	=	true;
								$this->table		=	$values;
							}
							
							$this->sql[]		=	"`".$this->table."`";
						}
					else
						$this->sql[]		=	"`".Safe::encode(implode("`,`",$values))."`";
						
					$this->execute();
					
					return $this;
				}
				
			public	function where($values = array(), $not = false, $group = false,$operand = 'and')
				{
					register_use(__METHOD__);
					
					if(empty($values))
						return $this;
					
					$this->sql_where	=	array();
					
					if(is_array($this->sql) && !in_array('WHERE',$this->sql))
						$this->sql[]	=	'WHERE';
					
					$equals				=	($not == false || $not == 0)? "=":"!=";
					
					if(is_array($values) && !empty($values)) {
							if(!isset($this->i))
								$this->i = 0;
								
							foreach($values as $key => $value) {
									
										$key	=	trim($key,":");
										
										if(isset($this->bind[":".$key])) {
												$auto	=	str_replace(".","_",$key).$this->i;
											//	$preop	=	$operand." ";
											}
										else {
											//	$preop	=	"";
												$auto	=	str_replace(".","_",$key);
											}
										
										$this->bind[":".$auto]	=	$value;
										$this->sql_where[]		=	$key." $equals ".":".$auto;
									//	$this->sql_where[]		=	$preop.$key." $equals ".":".$auto;
										$this->i++;
									}
							
							$this->sql[]	=	($group == false || $group == 0)? implode(" $operand ",$this->sql_where) : "(".implode(" $operand ",$this->sql_where).")";
						}
					else
						$this->sql[]		=	$values;
					
					if(is_array($this->bind))
						asort($this->bind);

					$this->execute();
					
					return $this;
				}
				
			public	function whereIn($col = false, $values = array())
				{
					register_use(__METHOD__);
					
					$this->sql_where	=	array();
					
					if(isset($this->sql) && !in_array('WHERE in',$this->sql))
						$this->sql[]		=	'WHERE `'.$col.'` in';
					
					if(is_array($values) && !empty($values)) {
							
							foreach($values as $key => $value) {
									$key	=	trim($key,":");
									$this->bind[":".str_replace(".","_",$key)]	=	$value;
									$this->sql_where[]		=	":".str_replace(".","_",$key);
								}
							
							$this->sql[]	=	"(".implode(",",$this->sql_where).")";
						}
					else {
							if(!empty($values))
								$this->sql[]		=	$values;
							else
								$this->valid_table	=	false;
						}
					
					if(is_array($this->bind))
						asort($this->bind);

					$this->execute();
					
					return $this;
				}
			
			public	function like($values = array('like'=>'','columns'=>array()), $match = false)
				{
					register_use(__METHOD__);
					
					if(is_array($this->sql) && !in_array("WHERE",$this->sql))
						$this->sql[]	=	'WHERE';

					//$this->sql[]	=	"(";
					if(!empty($values['columns'])) {
						if(is_array($values['columns'])) {
							foreach($values['columns'] as $key) {
								$key						=	trim($key,":");
								if($match == 'starts')	
									$this->bind[":".str_replace(".","_",$key)]	=	trim($values['like']."%");
								elseif($match == 'ends')
									$this->bind[":".str_replace(".","_",$key)]	=	trim("%".$values['like']);
								else
									$this->bind[":".str_replace(".","_",$key)]	=	trim("%".$values['like']."%");
								
								$this->sql_where[]	=	"`".$key."` like :".str_replace(".","_",$key);
							}
							
							$this->sql[]	=	implode(" or ",$this->sql_where);
						}
						else {
							if(!empty($values))
								$this->sql[]		=	$values;
						}
						
						$this->execute();
					}
					
					if(is_array($this->bind))
						asort($this->bind);
								
					return $this;
				}
			
			public	function addCustom($value = false,$reset = false)
				{
					register_use(__METHOD__);
					
					if($reset == true)
						$this->resetStored();

					if($value !== false) {
							$this->sql[]	=	$value;
						}
					
					$this->execute();
					return $this;
				}
			
			public	function whereDateSub($column = false, $whereVals = array("values"=>array(),"operand"=>"="), $interval = 1, $operand = ">", $now = true)
				{
					register_use(__METHOD__);
					
					if($column !== false) {
						$now			=	($now == true)? "NOW()":date("Y-m-d H:i:s",$now);
						$interval		=	(is_numeric($interval))? $interval:1;
						$operand		=	(	$operand == '<' ||
												$operand == '>' ||
												$operand == '=' ||
												$operand == '<=' ||
												$operand == '>=' ||
												$operand == '!='
											)? $operand:">";
						
						$this->sql[]	=	'WHERE';
						$this->sql[]	=	"`$column` $operand DATE_SUB($now, INTERVAL $interval MINUTE)";
						
						if(is_array($whereVals) && !empty($whereVals['values'])) {
							foreach($whereVals['values'] as $key => $value) {
								$key					=	trim($key,":");
								$this->bind[":".$key]	=	$value;
								$equals					=	(isset($whereVals['operand']) && !is_array($whereVals['operand']))? $whereVals['operand']:"="; 
								$this->sql_where[]		=	$key." $equals ".":".$key;
							}
								
							if(isset($this->sql_where))
								$this->sql[]	=	"and ".implode(" and ",$this->sql_where);
						}
						else
							$this->where_vals	=	$values;
							
						if(is_array($this->bind))
							asort($this->bind);
							
						$this->execute();
						
						return $this;
					}
				}
			
			public	function limit($value = false,$offset = false)
				{
					register_use(__METHOD__);
					
					$this->set_limit		=	"";
					
					if(is_numeric($value)) {
							
							$this->set_limit		=	$value;
							
							if(is_numeric($offset)) 
								$this->set_limit	=	$offset.", ".$this->set_limit;
						}
					
					$this->execute();
					
					return $this;
				}
			
			public	function orderBy($column = array())
				{
					register_use(__METHOD__);
					
					if(is_array($column) && !empty($column)) {
							foreach($column as $colname => $orderby) {
									$array[]	=	$colname." ".str_replace(array("'",'"',"+",";"),"",$orderby);
								}
						}
					else
						$array[]	=	$column;
						
					$this->orderby	=	implode(", ",$array);
					
					$this->execute();
					return $this;
				}
			
			public	function insert($value = "")
				{
					register_use(__METHOD__);
					
					// Reset all values
					$this->resetStored();
					
					$this->valid_table	=	$this->checkValidTables($value);
					
					$this->sql[]		=	"INSERT INTO ".$this->table;
					
					// Check to see how many columns there are
					if(!isset($this->table_cols[$value])) {
							$query	=	$this->DatabaseConfig->prepare("describe ".$value);
							$query->execute();
							
							while($columns = $query->fetch(PDO::FETCH_ASSOC)) {
									$this->table_cols[0][]	=	$columns['Field'];	
								}
						}
					
					$this->execute();
					return $this;
				}
			
			public	function update($value = "",$smartcheck = false)
				{
					register_use(__METHOD__);
					
					// Reset all values
					$this->resetStored();
					// Store table
					$this->table	=	$value;
					// If set, first check columns and filter publishable
					if($smartcheck == true) {
							$cols			=	$this->tableExists($value);
							$this->columns	=	$cols->publishable;
							$cols			=	NULL;
						}
					
					$this->sql[]	=	"update `".$value."`";
					$this->execute();
					return $this;
				}
			
			public	function set($values = "")
				{
					if(is_array($values) && !empty($values)) {
						$this->sql[]	=	"set";
						foreach($values as $key => $value) {
							if(!is_array($value)) {	
								$key	=	trim($key,":");
								$single	=	true;
								
								if((is_array($this->columns) && in_array($key,$this->columns)) || !isset($this->columns) || empty($this->columns)) {
									$this->sql_bind[]			=	"{$key} = :set{$key}";
									$this->bind[":set".$key]	=	$value;
								}
							}
							else {
								foreach($value as $subkey => $subvalue) {
									$subkey							=	trim($subkey,":");
									$this->bind[":set{$subkey}"]	=	$subvalue;
									$this->sql_bind[]				=	"{$subkey} = :set{$subkey}";
									$single	=	false;
								}
							}
						}
						
						if(is_array($this->sql_bind))
							asort($this->sql_bind);
						
						if(is_array($this->bind))
							asort($this->bind);
						
						if(!is_array($this->sql_bind))
							return $this;
						
						if($single)
							$this->sql[]	=	implode(", ",$this->sql_bind);
					}
						
					$this->execute();
					return $this;
				}
				
			// This has no filtering for post
			public	function setColumns($cols = false,$extract = false)
				{
					$compCols	=	array();
					
					if(is_array($cols)) {
							if($extract) {
									$cols	=	array_keys($cols);
									$cols	=	array_filter($cols);
								}
								
							foreach($cols as $cName)
									$compCols[]	=	"`".preg_replace("/[^0-9a-zA-Z\.\-\_]/","",$cName)."`";
							
							$this->sql[]	=	"(".implode(",",$compCols).")";
						}
					
					return $this;
				}
			
			// This function has no validation/filtering for post
			public	function setValues($values = array(),$forceblank = true)
				{
					if(!is_array($values) || (is_array($values) && empty($values)))
						return $this;
					
					$this->bind		=	array();
					$this->sql[]	=	"VALUES";
					
					$i	=	0;
					$a	=	0;
					
					foreach($values as $row) {
						if(!is_array($row))
							break;
						foreach($row as $key => $val) {
							
							$this->bind[":{$i}"]	=	(is_array($val))? json_encode($val) : $val;
							$assemble[$a][]			=	":{$i}";
							
							$i++;
						}
						
						$assemble[$a]	=	"(".implode(",",$assemble[$a]).")";
						$a++;
					}
					
					if(!empty($assemble))
						$this->sql[]	=	implode(", ", $assemble);

					return $this;
				}
			
			public	function columnsValues($columns = array(),$values = array(),$forceblank = true)
				{	
					register_use(__METHOD__);
					if(is_array($columns) && is_array($values) && !empty($columns) && !empty($values)) {
						// Create Columns
						$this->setColumns($columns);
						
						foreach($values as $key => $value) {
							if($key == 'ID')
								continue;
							
							$key	=	trim($key,":");
							
							if(is_array($this->table_cols[0]) && in_array($key,$this->table_cols[0])) {
								if(!is_array($value)) {
									if($forceblank) {
										// Check that the key is in the column
										if(in_array($key,$columns)) {				
											$this->bind[":".$key]	=	(is_array($value))? json_encode($value):$value;
											$this->sql_bind[]		=	":".$key;
											$single	=	true;
										}
									}
									else {
										$this->bind[":".$key]	=	(is_array($value))? json_encode($value):$value;
										$this->sql_bind[]		=	":".$key;
										$single	=	true;
									}
									
								}
								else {
									foreach($value as $subkey => $subvalue) {
										
										$subkey						=	trim($subkey,":");
										if($forceblank == true) {
											if(in_array($subkey,$columns)) {
												$this->bind[":".$subkey]	=	(is_array($value))? json_encode($subvalue):$subvalue;
												$this->sql_bind[]			=	":".$subkey;
												$single	=	false;
											}
										}
										else {
											$this->bind[":".$subkey]	=	(is_array($value))? json_encode($subvalue):$subvalue;
											$this->sql_bind[]			=	":".$subkey;
											$single	=	true;
										}
									}
								}
							}
						}
						
						if(is_array($this->bind))
							asort($this->bind);
							
						if(is_array($this->sql_bind))
							asort($this->sql_bind);
						
						if(!empty($single)) {
							$this->sql[]	=	"VALUES";
							$this->sql[]	=	"(".implode(",",$this->sql_bind).")";
						}
					}
						
					$this->execute();
					
					return $this;
				}
				
			public	function setAlt($array = false,$forceblank = true)
				{
					if(!is_array($array))
						return $this;
					
					$forceblank				=	(isset($forceblank) && is_bool($forceblank))? array("forceblank"=>$forceblank):array("forceblank"=>true);
					
					$this->sql[]			=	"SET";
					
					AutoloadFunction('validate_table_column');
					$this->column_layout	=	validate_table_column($this->table,$array,false,$forceblank);
					$this->bind				=	$this->column_layout['bind'];
					$this->sql[]			=	$this->column_layout['sql']['update']['vals'];
					return $this;
				}
			
			public	function fetch()
				{
					// Record queries
					if(nApp::getEngine('queries'))
						NubeData::$settings->engine->queries	+= 1;
					else
						NubeData::$settings->engine->queries	=	1;
					
					$fetchObj	=	false;
					$execute	=	true;
					
					$args_num	=	func_num_args();
					
					if($args_num > 0) {
							$settings	=	func_get_args();
							
							if(in_array("obj",$settings))
								$fetchObj	=	true;
								
							if(in_array("test",$settings))
								$execute	=	false;
						}
						
					if($execute) {
						if(isset($this->valid_table) && $this->valid_table != true) {
							$this->rowCount				=	0;
							$this->errors['validate'][]	=	'Table unavailable.';
							$this->err['validate'][]	=	'Table unavailable.';
							
							if(!$fetchObj) {
								return $this->results	=	0;
							}
								
							$this->execute();
							return $this;
						}
						
						$this->database	=	nApp::getDbName();
						// Try writing to the database
						try {
								$send			=	$this->DatabaseConfig->prepare($this->statement);
							}
						// If that fails, try a couple things...
						catch (PDOException $e) {
							if(preg_match('/column/i',$e->errorInfo[2])) {
								preg_match("/'(.*?)'/",$e->errorInfo[2],$match);
								// If the match is a column not available
								if(isset($match[1])) {
									// Filter the table name
									$useTable	=	$this->isValidNaming($this->table);
									// Try show tables in the database that are like table in question
									try
										{
											$showSql	=	"show tables in ".DatabaseConfig::$database." like '%".$useTable."%'";
											// Prepare/execute the statement
											$tSend		=	$this->DatabaseConfig->prepare($showSql);
											$tSend->execute();
											
											// Fetch the results
											while($data = $tSend->fetch(PDO::FETCH_ASSOC)) {
												$result[]	=	$data;
											}
											// If all is well, just alter the table....provide the user is infact an admin.
											if(!empty($result) &&  count($result) == 1) {
												$isValidTbl	=	$this->isValidNaming($match[1]);
												if(is_admin($isValidTbl)) {
													$alterSQL	=	"alter table `".$useTable."` add column `".$match[1]."` varchar(255) NULL";
													$aSend		=	$this->DatabaseConfig->prepare($alterSQL);
													$aSend->execute();
													
													nApp::saveError("table_error","auto_column");
													$send	=	$this->DatabaseConfig->prepare($this->statement);
												}
											}
											
											$send	=	(!isset($send))? false : $send;
										}
									catch(PDOException $er)
										{
											AutoloadFunction("QuickWrite");
											$author	=	(isset($_SESSION['username']))? $_SESSION['username'] : $_SERVER['REMOTE_ADDR'];
											$log	=	array("data"=>date("Y-m-d H:i:s")." / sql_add_column: Authored by ".$author." / ".$useTable." / ".$match[1]." /","dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.alter_table.txt","mode"=>"c+");
											QuickWrite($log);
											return 0;
										}
								}
							}
						}

						try
							{
								if(empty($send))
									return 0;
	
								(!empty($this->bind))? $send->execute($this->bind) : $send->execute();
								
								$error		=	array_filter($send->errorInfo());
								$err_rep	=	str_replace(0,"",$error[0]);
								
								if($err_rep) {
										AutoloadFunction('filter_error_reporting');
										$repArr	=	array('success'=>'fail', 'error'=>filter_error_reporting($error));
									}
								else
									$repArr	=	array('success'=>'ok', 'error'=>false);
									
								nApp::saveIncidental('nQuery', $repArr);
								
								if(empty($err_rep)) {
									if(is_admin()) {
										$this->errors['sql'][]	=	$error;
										$this->err['sql']		=	$error;
									}
								}
									
								if(!empty($this->statement) && is_admin()) {
									$user	=	(is_admin())? $_SESSION['username'] : $_SERVER['REMOTE_ADDR'];
									AutoloadFunction('QuickWrite,backtrace_file');
									QuickWrite(array("data"=>"sql: By ".$user." / ".$this->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__.PHP_EOL.strip_tags(printpre(backtrace_file(true))),"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.fetch.txt"));
								}
							}
						catch (PDOException $e)
							{
								if(is_admin()) {
									$errorCode						=	$e->getMessage();
									$this->errors['connection'][]	=	$errorCode;
									$this->err['connection']		=	$errorCode;
									AutoloadFunction('QuickWrite');
									$useNm	=	(!empty($_SESSION['username']))? $_SESSION['username'] : $_SERVER['REMOTE_ADDR'];
									QuickWrite(array("data"=>"sql: Author->{$useNm}->".$this->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.fetch_error.txt"));
								}
							}
						
						if(!$send)
							return 0;
						
						while($array = $send->fetch(PDO::FETCH_ASSOC)) {
							if(isset($this->unserial) && is_array($this->unserial)) {
								foreach($this->unserial as $ukey) {
									if(isset($array[$ukey]) && !empty($array[$ukey])) {
										try
											{
												$array[$ukey]	=	unserialize(Safe::decode($array[$ukey]));
											}
										catch (Exception $e)
											{
												$array[$ukey]	=	false;
												if(is_admin()) {
													printpre($e);
												}
											}
											
										break;
									}
								}
							}
							
							$rows[]			=	$array;
						}
						
						$this->rowCount	=	(isset($rows) && is_array($rows))? count($rows):0;	
						
						if(isset($rows)) {
							if($fetchObj == true) {
								$this->results	=	$rows;
								return $this;
							}
							else
								return $rows;
						}
						else {	
							if($fetchObj == true) {
								$this->results	=	0;
								return $this;
							}
							else
								return 0;
						}
					}
						
					$this->execute();
						
					return $this;
				}
			
			public	function noValidate()
				{
					$this->valid_table	=	true;
					return $this;
				}
			
			public	function write($write = false)
				{
					$this->execute();
					try
						{
							// Prepare statement
							$send	=	$this->DatabaseConfig->prepare($this->statement);
							
							if(nApp::getEngine('queries')) {
								NubeData::$settings->engine->queries	+= 1;
							}
	
							if(!empty($this->bind)) {
								$send->execute($this->bind);
								$_error['binding']	=	$send->errorInfo();
							}
							else
								$send->execute();
								
							$error		=	$send->errorInfo();
							$err		=	str_replace(0,"",$error[0]);
							
							if(!empty($err)) {
								nApp::saveToLogFile('write'.date('YmdHis').time().".log",array($error));
								if(is_admin()) {
									$this->errors['sql'][]	=	$error;
									$this->err['sql']		=	$error;
								}
							}
							else {
								if(is_admin()) {
									$this->errors['sql'][]	=	'OK';
									$this->err['sql']		=	'OK';
								}
							}
							
							AutoloadFunction('QuickWrite');
							if(!empty($this->statement)) {
								$user	=	(!empty($_SESSION['username']))? $_SESSION['username']."::".$_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR'];
								QuickWrite(array("data"=>"sql: By ".$user." / ".$this->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.write.txt"));
							}
						}
					catch (PDOException $e)
						{
							if(is_admin()) {
								$errorCode						=	$e->errorInfo;
								$this->errors['connection'][]	=	$errorCode;
								$this->err['connection']		=	$errorCode;
							}
						}
								
					return $this;
				}
				
			protected	function checkValidTables($tablename = false)
				{
					if(!nApp::getTables()) {
						if(!isset($this->valid_tables)) {
							$database	=	nApp::getDbName();
							
							if(!$database) {
								$this->valid_tables	=	array();
								return false;
							}
							
							$showSQL	=	"SHOW tables in ".$database;
							$fetch	=	$this->DatabaseConfig->prepare($showSQL);
							$fetch->execute();
							
							while($rows = $fetch->fetch(PDO::FETCH_ASSOC)) {
								$results[]	=	$rows;
							}
								
							$results	=	(!isset($results) || (isset($results) && empty($results)))? 0:$results;
							
							if($results != 0) {
								foreach($results as $object)
									$all_tables[]	=	$object['Tables_in_'.$database];
								
								if(isset($all_tables))
									$this->valid_tables	=	$all_tables;
							}
						}
	
						$this->valid_tables	=	(!is_array($this->valid_tables) || (!isset($this->valid_tables)))? array():$this->valid_tables;
					}
					else
						$this->valid_tables	=	nApp::getTables();
					
					$this->table		=	preg_replace('/[^0-9a-zA-Z\.\_\-\`]/','',$tablename);
					$thisTable			=	(in_array($this->table,Safe::to_array($this->valid_tables)));
					
					if(empty(nApp::getTables())) {
						nApp::saveSetting('tables',$this->valid_tables);
					}
					
					return $thisTable;
				}
				
			public function	describe($table = false)
				{
					// Reset all values
					$this->resetStored();
					$this->sql[]	=	"DESCRIBE `".$table."`";
					$this->execute();
					return $this;
				}
				
			public	function checkTableEmpty($table = "",$as_name = "count")
				{
					register_use(__METHOD__);
					
					// Reset all saved settings
					$this->resetStored();
					
					if(!empty($table)) {
						$this->statement	=	"SELECT COUNT(*) as {$as_name} FROM ".$table;
						$count				=	$this->DatabaseConfig->prepare($this->statement);
						$count->execute();
						$num				=	$count->fetch(PDO::FETCH_ASSOC);
						$this->rowCount 	=	$num['count'];
						
						if(nApp::getEngine('queries')) {
							NubeData::$settings->engine->queries	+= 1;
						}
					}
					else
						$this->rowCount	=	'invalid';
						
					return $this;
				}
			
			public	function Record($record = false)
				{
					if(defined('SERVER_MODE') && SERVER_MODE) {
						if($record != false) { 
							AutoloadFunction('backtrace_file');
							$this->recordata[0]	=	$record;
							$this->recordata[1]	=	backtrace_file();
						}
					}
					else
						$this->recordata	=	false;
					
					return $this;
				}
			
			public	function execute()
				{
					$limit				=	(!empty($this->set_limit))? " LIMIT ".$this->set_limit:"";
					$order				=	(!empty($this->orderby))? " ORDER BY ".$this->orderby:"";
					$this->statement	=	(!empty($this->sql))? implode(" ", Safe::to_array($this->sql)).$order.$limit:"";
	
					return $this;
				}
				
			public	function tableExists($table = false, $type = 'post',$break = false)
				{
					$this->resetStored();
					
					if($table) {
						// Save to query recorder
						global	$_dbrun;
						
						AutoloadFunction('get_tables_in_db');
						$tables		=	get_tables_in_db();
						
						if(!is_array($tables)) {
							$check		=	$this->DatabaseConfig->prepare($_sql = "show tables like :table");
							$check->execute(array(":table"=>$table));
							$table_exists	=	$check->fetch(PDO::FETCH_ASSOC);
							
							$_dbrun[]	=	$_sql;						
							if(nApp::getEngine('queries')) {
								NubeData::$settings->engine->queries	+= 1;
							}
						}
						else
							$table_exists	=	in_array($table,$tables);
						
						$this->table	=	(!empty($table_exists))? $table_exists:false;
						if($this->table != false) {
							$this->rowCount	=	1;
							$this->results	=	1;
						}
						else {
							$this->rowCount	=	0;
							$this->results	=	0;
						}
						
						if($break)
							return $this;
						
						if($this->table != false) {
							$this->statement	=	"describe ".$table;
							$query				=	$this->DatabaseConfig->prepare($this->statement);
							$query->execute();
							$error				=	$query->errorInfo();
							$err				=	str_replace(0,"",$error[0]);
							
							if(is_admin()) {
								$this->errors['sql'][]	=	$error;
								$this->err['sql']		=	$error;
							}
							
							if(!is_array($type)) {
								if($type == 'post')
									$request	=	$_POST;
								elseif($type == 'get')
									$request	=	$_GET;
								elseif($type == 'request')
									$request	=	$_REQUEST;
							}
							else
								$request	=	$type;
							
							$this->columns_in_table	=	array();
							
							while($result = $query->fetch(PDO::FETCH_ASSOC)) {
								// Auto-increment check
								$extra	=	$result['Extra'];
								$key	=	$result['Field'];
								
								// List column names that are not allowed to be null
								$null	=	(isset($result['Null']) && strtolower($result['Null']) == 'no');
								// Capture all pages
								
								$this->columns_in_table[]	=	$key;
								
								if(isset($request[$key])) {
									// If the column is not an auto incremented column
									$allow[1]	=	($extra == 'auto_increment')? 1:0;
									// If the column is empty and null not allowed
									$allow[2]	=	(empty($request[$key]) && $null == true)? 1:0;
									// If passed, all is good to save array key
									if(array_sum($allow) == 0) {
										$this->publishable[]	=	$key;
										$this->bind[":".$key]	=	$request[$key];
										$this->data[$key]		=	$request[$key];
										$this->matched[$key]	=	$request[$key];
									}
								}
							}
						}
					}
							
					if(is_array($this->bind))
						asort($this->bind);
					
					// Save allowable columns
					$this->publishable		=	(isset($this->publishable) && !empty($this->publishable))? array_unique(array_values($this->publishable)): 0;
					// Sort the array
					if(is_array($this->publishable))
						asort($this->publishable);
						
					$this->columns_in_table	=	(isset($this->columns_in_table))? array_unique($this->columns_in_table) : 0;
					
					return $this;
				}

			public	function fetchTablesInDB($database = false)
				{
					register_use(__METHOD__);
					
					$this->resetStored();
					
					$this->sql[]	=	"show tables in";
					$this->sql[]	=	($database != false)? "`".Safe::encodeSingle($database)."`" : NubeData::$settings->connection->database;
					
					$this->execute();
						
					return $this;
				}
			
			public	function addParams($array = false)
				{
					if(empty($array) || !is_array($array))
						return $this;
					
					foreach($array as $key => $value) {
						$key	=	trim($key,":");
						$this->bind[":".$key]	=	$value;
					}
					
					return $this;
				}
			
			public	function getStatement()
				{
					$this->execute();
					return (!empty($this->statement))? $this->statement : "";
				}
			
			public	function alter($table = false)
				{
					if(empty($table))
						return $this;
					$this->sql		=	array();
					$this->sql[]	=	"ALTER TABLE";
					$this->sql[]	=	"`{$table}`";
					
					$this->execute();
					
					return $this;
				}
			
			public	function addUnique($column = false,$name = "unique_index")
				{
					if(empty($column))
						return $this;
						
					$multi	=	false;
					if(is_array($column)) {
						if(count($column) > 1) {
							$count	=	"`{$name}` (`".implode("`, `",$column)."`)";
							$multi	=	true;
						}
						else
							$column	=	"`".implode("",$column)."`";
					}
					else
						$column	=	"`{$column}`";
					
					$this->sql[]	=	(!$multi)? "ADD UNIQUE ({$column})" : "ADD UNIQUE ".$count;
					
					return $this;
				}
			
			public	function modifyColumn($column = false,$opts = false)
				{
					if(empty($column))
						return $this;
					
					$command	=	'MODIFY COLUMN';
					
					if(!in_array($command,$this->sql))
						$this->sql[]	=	$command;
						
					$this->sql[]	=	"`{$column}`";
	
					if(is_array($opts) && !empty($opts))
						$this->sql[]	=	implode(" ",$opts);
					
					return $this;
				}
			
			public	function addColumn($array = false)
				{
					if(empty($array) || !is_array($array))
						return $this;
					
					$comma			=	(in_array('ADD COLUMN',$this->sql))? ',':"";
					$this->sql[]	=	$comma.'ADD COLUMN';
					if(!empty($array['name']))
						$this->sql[]	=	"`".$array['name']."`";
					if(!empty($array['type']))
						$this->sql[]	=	$array['type'];
					if(!empty($array['count']) && is_numeric($array['count']))
						$this->sql[]	=	"(".$array['count'].")";
					if(isset($array['null']))
						$this->sql[]	=	($array['null'])? "NULL" : "NOT NULL";
					if(isset($array['ai']))
						$this->sql[]	=	($array['ai'])? "AUTO_INCREMENT" : "";
					if(isset($array['primary']))
						$this->sql[]	=	($array['primary'])? "PRIMARY KEY" : "";
					
					if(!empty($array['ai_set']) && is_numeric($array['ai_set']))
						$this->sql[]	=	", AUTO_INCREMENT = ".$array['ai_set'];
					
					$this->execute();
					
					return $this;
				}
			
			public	function afterColumn($column = false)
				{
					if(!empty($column))
						$this->sql[]	=	"AFTER `".$column."`";
						
					$this->execute();
					
					return $this;
				}
				
			public	function beforeColumn($column = false)
				{
					if(!empty($column))
						$this->sql[]	=	"BEFORE `".$column."`";
					
					$this->execute();
					
					return $this;
				}
			
			public	function query($sql = false,$params = false)
				{
					
					if(empty($sql) && empty($this->sql))
						return $this;
						
					$this->execute();
					
					$this->sql	=	(!empty($sql))? $this->statement : $sql;
					
					try {
						$send		=	$this->DatabaseConfig;
						
						if(is_array($params) && !empty($params)) {
							$this->bind	=	array();
							$this->addParams($params);
						}
						
						if(!empty($this->bind)) {
							// Prepare statement
							$this->query				=	$send->prepare($sql);
							$this->query->execute($this->bind);
							$_error['binding']			=	$send->errorInfo();
							$this->errors['q_sql'][]	=	$send->errorInfo();
						}
						else {
							$this->query				=	$send->query($sql);
							$this->errors['q_sql'][]	=	$send->errorInfo();
						}
					}
					catch (PDOException $e) {
						$this->errors[]	=	$e->getMessage();
						$this->query	=	false;
					}
						
					$this->sql	=	array();
	
					return $this;
				}
			
			public	function getResults($obj = false)
				{
					if(!empty($this->query)) {
						while($row = $this->query->fetch(PDO::FETCH_ASSOC))
							$result[]	=	$row;
					}

					if(!empty($result))
						return ($obj)? Safe::to_object($result) : $result;
					else
						return 0;
				}
			
			function useColumns($array = false)
				{
					if(empty($array))
						return $this;
					
					$this->sql[]	=	(is_array($array))? "(`".implode("`,`",$array)."`)" : "(`{$array}`)";
					$this->execute();
					return $this;
				}
			
			function dropColumn($column = false,$table = false)
				{
					if(empty($column))
						return $this;
					
					if((is_array($this->sql) && !in_array("ALTER TABLE",$this->sql)) || !is_array($this->sql)) {
						if(empty($table))
							return $this;
							
						$this->sql		=	array();
						$this->sql[]	=	"ALTER TABLE `{$table}`";
						$this->table	=	$table;
					}
						
					$this->sql[]	=	"DROP COLUMN `{$column}`";

					$this->execute();
					
					return $this;
				}
			
			public	function isValidNaming($table = false,$regex = '/[^a-zA-Z0-9\-\_\.]/')
				{
					$table	=	preg_replace($regex,"",$table);
					return (empty($table))? false : $table;
				}
		}