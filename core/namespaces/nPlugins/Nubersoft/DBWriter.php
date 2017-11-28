<?php
namespace nPlugins\Nubersoft;

class	DBWriter extends \Nubersoft\nApp
{
	protected	$payload,
				$cols_in_table,
				$PasswordEngine,
				$table;

	public	function __construct()
	{
		$this->PasswordEngine	=	new CreateUser();
		return parent::__construct();
	}

	protected	function rewrite_htaccess()
	{
		if(!empty($this->payload["requestTable"])) {
			$this->autoload(array("check_empty"));
			if(check_empty($this->payload,'requestTable','system_settings') && !empty($this->payload['content']['htaccess'])) {
				$this->autoload(array("get_default_htaccess"));
				get_default_htaccess(array("htaccess"=>self::call('Safe')->decode($this->payload['content']['htaccess']),"write"=>true));
			}
		}
	}

	public	function execute($payload = false)
	{
		$this->autoload(array('is_admin','check_empty','call_action','compare','nquery'));
		$uploads		=	false;
		$nubquery		=	nquery();
		$this->payload	=	(is_array($payload))? $payload:$_POST;
		// Set the table by submission
		$useTable		=	(!empty($this->payload['requestTable']))? $this->payload['requestTable']: $this->getDefaultTable();
		// Reset all tables
		$this->resetTableAttr($useTable);
		$_table			=	$this->getDefaultTable();			
		// Get table columns
		$this->cols_in_table	=	$nubquery->tableExists($_table);
		// Create / Update password
		$this->payload			=	$this->PasswordEngine	->check($this->payload)
															->execute(true)
															->payload;	
		// If the action is insert, remove the ID
		if(call_action('add',$this->payload)) {
			if(isset($this->payload['ID']))
				unset($this->payload['ID']);
		}
		// Do an htaccess rewrite on system_settings
		$this->rewrite_htaccess();
		$_checkAction[]		=	($this->getRequest('add') || isset($this->payload['add']))? 1:0;
		$_checkAction[]		=	($this->getRequest('update') || isset($this->payload['update']))? 1:0;
		// Add a unique id
		if(call_action('add',$this->payload)) {
			autoload_function('fetch_unique_id');
			$this->payload['unique_id']	=	(empty($this->payload['unique_id']))? fetch_unique_id(rand(1000,9999)):$this->payload['unique_id'];
		}
		// Go at it provided all is well...
		if($nubquery && in_array(1,$_checkAction)) {
			// If requestTable is set and usergroup permitted
			if(!empty($_table)) {// && array_sum($_allow) >= 1
				// If delete is set
				if(check_empty($this->payload,'delete','on')) {
					// See if image is associated with row
					$checkImage	=	$nubquery	->select()
												->from($_table)
												->where(array("ID" =>$this->payload['ID']))
												->getResults();
					if($checkImage != 0) {
						if(isset($checkImage[0]['file_name']) && isset($checkImage[0]['file_name'])) {
							autoload_function('delete_upload');
							delete_upload(array("file_name"=>$checkImage[0]['file_name'],"file_path"=>$checkImage[0]['file_path']));
							if(defined("NBR_THUMB_DIR")) {
								$thumbnail	=	new ImageFactory();
								$thumbnail	->SearchLocation(NBR_THUMB_DIR)
											->SearchFor(NBR_THUMB_DIR."/".$checkImage[0]['file_name'])
											->ScrapThumbnails();
							}
						}
					}
				}

				// Upload files and return files array
				$this->autoload('upload_file',NBR_FUNCTIONS);
				$naming		=	(check_empty($this->payload,'keep_name','1'))? array("name"=>1,"payload"=>$this->payload,"table"=>$_table):array("payload"=>$this->payload,"table"=>$_table);
				$uploads	=	upload_file($naming);

			//	die(printpre($uploads));
			}
		}

		// Process Tables that have file columns
		if($uploads) {
			// See if action is add
			if(call_action('add',$this->payload)) {
				// See if the count is greater than 1
				if(compare($uploads['count'],1,">") && is_array($uploads['files'])) {
					foreach($uploads['files'] as $key => $files_to_add) {
						$this->writeFileInstance($files_to_add);
					}
				}
				elseif(compare($uploads['count'],1,"="))
					$this->writeFileInstance($uploads['files'][0]);
			}
			else
				$this->writeFileInstance($uploads['files'][0],'update');

			// Stop the process from continuing
			return;
		}
		// Action is to add into db
		if(call_action('add',$this->payload)) {
			$this->writeFileInstance($this->payload,'insert');
		}
		// Action is to update row
		elseif(call_action('update',$this->payload)) {
			// Check if delete is on
			if(check_empty($this->payload,'delete','on') && !empty($this->payload['ID'])) {
				$nubquery	->delete()
							->from(parent::$settings->engine->table_name)
							->where(array("ID"=>$this->payload['ID']))
							->write();
			}
			else {
				if(!empty($this->payload['ID']))
					$this->writeFileInstance($this->payload,'update');
			}
		}
	}

	protected	function writeFileInstance($files_to_add = false,$type = 'insert')
	{ 
		if(!is_array($files_to_add))
			return;
		// Set the required functions for this action
		$actions[]	=	'filter_action_words';
		$actions[]	=	'combine_arrays';
		$actions[]	=	'compare_post_data';
		$actions[]	=	'check_empty';
		$actions[]	=	'call_action';
		$actions[]	=	'QuickWrite';
		autoload_function($actions);
		// Create new query for sake of ease
		$query		=	nquery();
		// Assign Table
		$table		=	(!empty($this->table))? $this->table : $this->getDefaultTable();
		if(check_empty($this->payload,'override','1'))
			$allowEmpty	=	true;
		elseif(is_admin())
			$allowEmpty	=	true;
		else
			$allowEmpty	=	false;
		// This is for writing to logs
		$author		=	(is_loggedin())? $_SESSION['username'] : $_SERVER['REMOTE_ADDR'];
		if(isset($this->payload['override']))
			unset($this->payload['override']);
		// Assign a unique_id if not already set
		if(empty($this->payload["unique_id"])) {
			autoload_function('fetch_unique_id');
			$files_to_add["unique_id"]	=	fetch_unique_id(rand(1000,9999));
		}
		// Get columns from table
		$this->cols_in_table	=	$this->getColumns($table);

		$getDiff	=	function($cols,$payload)
			{
				$pCols		=	array_keys($payload);
				$aDiff		=	array_diff($cols,$pCols);
				$allowed	=	array_diff($cols,$aDiff);

				if(!empty($allowed)) {
					foreach($allowed as $key) {
						$new[$key]	=	$payload[$key];
					}
				}

				return (!empty($new))? $new : $payload;
			};
		// Filter out all resvered keys from post array
		$filter_post	=	$getDiff($this->cols_in_table,$this->payload);
		// Filter out all resvered keys from file array
		$files_to_add	=	array_diff_key($files_to_add,filter_action_words('key'));
		// Combine all arrays and filter out empty
		$final			=	combine_arrays($files_to_add,$filter_post,$allowEmpty);
		// Filter out empty columns
		//$cols			=	compare_post_data($this->cols_in_table->columns_in_table,$final);
		if($type == 'insert') {
			$query	->insert($table)
					->setColumns(array_keys($final))
					->setValues(array($final))
					->write();

			//die(printpre($query));

			// Write sql disk
			if(!empty($query->statement)) {
				$log	=	array("data"=>"sql_write: Authored by ".$author." / ".$query->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__.PHP_EOL.'ERRORS: '.stip_tags(printpre($query->errors)),"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.write.txt","mode"=>"c+");
			}
		}
		else {
			// If delete, delete
			if(check_empty($this->payload,'delete','on')) {
				$query	->delete()
						->from($table)
						->where(array("ID"=>$this->payload['ID']))
						->write();// Write sql disk

				if(!empty($query->statement))
					$log	=	array("data"=>"sql_delete: By ".$author." / ".$query->statement.PHP_EOL.PHP_EOL."FILE/LINE: ".__FILE__."->".__LINE__,"dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.delete.txt","mode"=>"c+");
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
					$log	=	array("data"=>"sql_update: Authored by ".$author." / ".$query->statement."/","dir"=>NBR_CLIENT_DIR."/settings/error_log/","filename"=>"sql.update.txt","mode"=>"c+");
			}
		}

		if(!empty($query->err)) {
			$errs	=	$this->toObject($query->err);
			$var	=	(!empty($errs->connection->{2}))? $errs->connection->{2} : false;
			$this->saveIncidental('nquery', array('success'=>'fail','error'=>printpre($query)));//filter_error_reporting($var)
			$log	=	array(
				"content"=>"SQL: Authored by ".$author." / ".__FILE__."->".__LINE__.PHP_EOL.'ERRORS: '.strip_tags(printpre($query->errors)),
				"save_to"=>NBR_CLIENT_DIR."/settings/error_log/sql_errors.txt",
				"type"=>"c+"
			);
		}

		// Write to a log
		if(!empty($log)) {
			self::call('nFileHandler')->writeToFile($log);
		}

		return $this;
	}

	public	function useTable($table)
	{
		$this->table	=	$table;
		return $this;
	}
}