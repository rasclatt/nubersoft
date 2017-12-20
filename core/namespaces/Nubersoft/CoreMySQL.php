<?php
namespace Nubersoft;

class	CoreMySQL extends \Nubersoft\nApp
{
	public		static	$CoreAttributes;
	protected	static	$con;

	public	function initialize($_page = true)
	{
		if(DatabaseConfig::$con) {
			# Check to see if the site has been turned off or not
			# If site live not on
			if(!$this->siteLive()){
				# Check for first time install HTACCESS FILE.
				# If not there but there is content for one, create it.
				if(!is_file(NBR_ROOT_DIR.DS.'.htaccess')) {
					$this
						->getHelper('nReWriter')
						->createHtaccess(array(
							'content'=>$this->safe()->decode($content['htaccess']),
							'save_to'=>NBR_ROOT_DIR
						));
				}
			}
			# Fetch the page results
			$result	=	$this->fetchPage();
			# If the statement produces results build variables
			if($result != 0) {
				$unique_id				=	$result['unique_id'];
				self::$CoreAttributes	=	$result;
			}
		}

		self::$CoreAttributes	=	(isset(self::$CoreAttributes))? self::$CoreAttributes : array();
	}

	protected	function fetchPage()
	{
		$result	=	$this->getPageURI();

		# If the page could not be matched, make sure table exists
		if(!$result) {
			if($this->isAdmin())
				$_createTable	=	\FetchRemoteTable::create('main_menus');

			$_incidental['404']		=	true;
			$result['page_valid']	=	$result['unique_id']	=	false;
			$result['page_live']	=	$result['auto_cache']	=	$result['session_status']	=	'off';
		}
		else
			$result['page_valid']	=	true;

		return $result;
	}

	public	function getTableRows($tables = false)
	{
		if(!empty($tables))
			$tables	=	(is_array($tables))? $tables : array($tables);
		else
			$tables	=	$this->getDataNode('tables');

		foreach($tables as $table) {
			$rows	=	$this->nQuery()->query("SELECT * FROM `{$table}`")->getResults();


			if($rows == 0)
				continue;

			if(isset($rows[0]['ID']))
				unset($rows[0]['ID']);

			$sql[]	=	"INSERT INTO `{$table}` (`".implode('`,`',array_keys($rows[0]))."`)";
			$sql[]	=	"VALUES";
			foreach($rows as $row) {
				if(isset($row['ID']))
					unset($row['ID']);

				$new[]	=	"('".implode("', '",$row)."')";
			}

			$sql[]	=	implode(','.PHP_EOL,$new);

			$tableRows[$table]	=	implode(' ',$sql);

			$new	=	array();
			$sql	=	array();
		}

		return (!empty($tableRows))? $tableRows : false;
	}

	public	function savePhpFile($array,$dir = false)
	{
		$string	=	'<?php'.PHP_EOL;
		foreach($array as $table => $sql) {
			$string	.=	'$data[\''.$table.'\']	=	"'.$sql.'";'.PHP_EOL.PHP_EOL;
		}

		if(!empty($dir)) {
			if($this->isDir(pathinfo($dir,PATHINFO_DIRNAME)))
				$this->saveFile($string,$dir);
		}
		else {
			return $string;
		}
	}

	public	function getTablesCreate($tables = false)
	{
		if(!empty($tables))
			$tables	=	(is_array($tables))? $tables : array($tables);
		else
			$tables	=	$this->getDataNode('tables');

		foreach($tables as $table) {
			$sqlArr	=	$this->nQuery()->query("SHOW CREATE TABLE `{$table}`")->getResults(true);
			if(!isset($sqlArr['Create Table']))
				continue;

			$tableArr[$table] =	$sqlArr['Create Table'];
		}
		return (isset($tableArr))? $tableArr : false;
	}

	public	function saveDatabaseScheme($dir = false,$append = false)
	{
		$dir	=	(empty($dir))? NBR_CLIENT_DIR.DS.'database_scheme' : $dir;

		if(!$this->isDir($dir)){
			trigger_error('Could not create folder.',E_USER_NOTICE);
			return;
		}

		if(!is_file($file = $this->toSingleDs($dir.DS.$append.'tables.php'))) {	
			$this->savePhpFile($this->getTablesCreate(),$file);
		}
		if(!is_file($file = $this->toSingleDs($dir.DS.$append.'rows.php'))) {	
			$this->savePhpFile($this->getTableRows(),$file);
		}
	}

	public	function installTable($table,$sql = false)
	{
		if(empty($sql)) {
			include($this->getInstallerDir().DS.'tables.php');
			$sql	=	(isset($data[$table]))? $data[$table] : false;
		}

		if(empty($sql))
			return false;

		$this->nQuery()->query($sql);
	}

	public	function installAllTables()
	{
		include($this->getInstallerDir().DS.'tables.php');
		foreach($data as $table => $create) {
			$this->installTable($table,$create);
		}

		return $this;
	}

	public	function getInstallerDir()
	{
		return NBR_SETTINGS.DS.'firstrun'.DS.'sql';
	}

	public	function installAllRows()
	{
		include($this->getInstallerDir().DS.'rows.php');
		foreach($data as $table => $create) {
			$this->installRows($table,$create);
		}

		return $this;
	}

	public	function installRows($table,$sql = false)
	{
		if(empty($sql)) {
			include($this->getInstallerDir().DS.'rows.php');
			$sql	=	(isset($data[$table]))? $data[$table] : false;
		}

		if(empty($sql))
			return false;

		$this->nQuery()->query($sql);

		return $this;
	}

	public	function resetTable($table = false)
	{
		if(empty($table)) {
			if(!$this->isAdmin())
				return false;

			$table	=	$this->safe()->sanitize($this->getPost('requestTable'));
		}

		$this->saveDatabaseScheme(false,date('YmdHis').'_');
		$this->saveIncidental('resetTable',array('msg'=>'Your rows and create sql have been exported.'));

		$rows	=	$this->hasTableRows($table);

		if(empty($rows)) {
			$this->resetIdColumn($table);
			$this->saveIncidental('resetTable',array('msg'=>'Table has no default rows to reset.'));
			return false;
		}

		$this->nQuery()->query("delete from `{$table}`")->write();
		$this->saveIncidental('resetTable',array('msg'=>'Rows in `'.$table.'` were deleted.'));
		$this->installRows($table);
		$this->resetIdColumn($table);
		$this->saveIncidental('resetTable',array('msg'=>'Rows installed and cleaned up.'));

		return true;
	}

	public	function hasTableRows($table)
	{
		include($this->getInstallerDir().DS.'rows.php');
		return (isset($data[$table]))? $data[$table] : false;
	}

	public	function resetIdColumn($table,$col = 'ID')
	{
		$this->nQuery()->query("ALTER TABLE `{$table}` DROP COLUMN `{$col}`");
		$this->nQuery()->query("ALTER TABLE {$table} 
ADD COLUMN {$col} INT NOT NULL AUTO_INCREMENT FIRST,
ADD UNIQUE INDEX {$col}_UNIQUE ({$col} ASC);");
	}
}