<?php
namespace Nubersoft;

class nQuery extends \Nubersoft\nApp
{
	private	static	$con;
	private	$query,
			$sql,
			$bind,
			$stmt;
	
	public	function getConnection()
	{
		if(self::$con instanceof \Nubersoft\Database)
			return self::$con;
		
		include_once(NBR_DATABASE_CREDS);
		
		if(!defined('DB_HOST'))
			throw new \Exception('Required database info not set', 9090);
		
		try {
			$Db	=	new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
			self::$con	=	$Db->getConnection();
			return self::$con;
		}
		catch(\PDOException $e) {
			throw new HttpException($e->getMessage(), 103);
		}
	}
	
	public	function query($sql, $bind = false)
	{
		$con	=	$this->getConnection();
		
		if($bind) {
			$bArr			=	array_values($bind);
			$this->query	=	$con->prepare($sql);
			$this->query->execute($bind);
		}
		else {
			$this->query	=	$con->query($sql);
		}
		
		return $this;
	}
	
	public	function getResults($single = false)
	{
		while($results = $this->query->fetch(\PDO::FETCH_ASSOC)) {
			$row[]	=	$results;
		}
		if(empty($row))
			return [];
		
		return ($single)? $row[0] : $row;
	}
	
	public	function insert($table, $ticks = '`')
	{
		$this->sql		=	[];
		$this->sql[]	=	"INSERT INTO {$ticks}{$table}{$ticks}";
		return $this;
	}
	
	public	function columns($columns, $ticks = '`')
	{
		$this->sql[]	=	"({$ticks}".implode("{$ticks}, {$ticks}", $columns)."{$ticks})";
		return $this;
	}
	
	public	function values($rows)
	{
		$this->bind		=	[];
		$this->sql[]	=	"VALUES";
		foreach($rows as $row) {
			$this->bind	=	array_merge($this->bind, $row);
			$cols[]	=	"(".implode(', ',array_fill(0,count($row), '?')).")";
		}
		
		if(!empty($cols))
			$this->sql[]	=	implode(','.PHP_EOL, $cols);
		
		return $this;
	}
	
	public	function write()
	{
		$this->stmt	=	implode(PHP_EOL, $this->sql);
		$this->query($this->stmt, $this->bind);
	}
	
	public	function select($columns = '*')
	{
		$this->sql		=	[];
		$this->sql[]	=	"SELECT";
		
		if(is_array($columns))
			$columns	=	implode(', ', $columns);
		
		$this->sql[]	=	$columns;
		
		return $this;
	}
	
	public	function from($table)
	{
		if(is_array($table))
			$table	=	implode(', ', $table);
		
		$this->sql[]	=	'FROM '.$table;
		
		return $this;
	}
	
	public	function where($where, $bind = false)
	{
		$this->bind		=	[];
		$this->sql[]	=	'WHERE';
		if(!is_array($where)) {
			$this->sql[]	=	$where;
			if(!empty($bind))
				$this->bind	=	(is_array($bind))? array_merge($this->bind, $bind) : $bind;
			return $this;
		}
		
		foreach($where as $cond) {
			$column			=	(!empty($cond['c']))? $cond['c'] : false;
			$value			=	(!empty($cond['v']))? $cond['v'] : false;
			$operand		=	(!empty($cond['op']))? $cond['op'] : '=';
			$condition		=	(!empty($cond['co']))? $cond['co'] : '';
			$this->bind[]	=	$value;
			$this->sql[]	=	$column.' '.$operand.' ? '.$condition;
		}
		
		return $this;
	}
	
	public	function orderBy($array)
	{
		$this->sql[]	=	'ORDER BY';
		
		if(!is_array($array)) {
			$this->sql[]	=	$array;
			return $this;
		}
		
		$rows	=	[];
		foreach($array as $key => $value) {
			$rows[]	=	$key.' '.$value;
		}
		
		$this->sql[] =	implode(', ', $rows);
		
		return $this;
	}
	
	public	function addStmt($sql)
	{
		$this->sql[]	=	$sql;
		return $this;
	}
	
	public	function fetch($one = false)
	{
		$sql	=	implode(PHP_EOL, $this->sql);
		$bind	=	(!empty($this->bind))? $this->bind : null;
		try {
			$data	=	$this->query(implode(PHP_EOL, $this->sql), $bind)->getResults($one);
			return $data;
		}
		catch (\PDOException $e) {
			$this->toError($e->getMessage().'stmt: '.$sql, 'sql');
		}
	}
}