<?php
namespace Nubersoft;

use \Nubersoft\Dto\Database\ConstructRequest as DbSettings;

class nQuery extends nApp
{
    private static $con;
    private $query, $sql, $bind, $stmt;
    
    public function getConnection(DbSettings $Settings = null)
    {
        if(empty($Settings)) {
            if(self::$con instanceof Database)
                return self::$con;

            include_once(NBR_DATABASE_CREDS);
        
            if(!defined('DB_HOST'))
                throw new HttpException\Core('Required database credentials not set or are missing.', 9090);
            
            $Settings = (empty($Settings))? new DbSettings() : $Settings;
            $Settings->host = DB_HOST;
            $Settings->dbname = DB_NAME;
            $Settings->user = DB_USER;
            $Settings->pass = DB_PASS;
            $Settings->charset = DB_CHARSET;
        }
            
        try {
            return self::$con = (new Database($Settings))->getConnection();
        }
        catch(\PDOException $e) {
            throw new HttpException($e->getMessage(), 103);
        }
    }
    
    public function query($sql, $bind = false, $conn = false)
    {
        $con = ($conn instanceof \PDO)? $conn : $this->getConnection();
        
        if(is_array($bind)) {
            $bArr = array_values($bind);
            $this->query = $con->prepare($sql);
            $this->query->execute($bind);
        }
        else {
            $this->query = $con->query($sql);
        }
        
        return $this;
    }
    
    public function getResults($single = false)
    {
        while($results = $this->query->fetch(\PDO::FETCH_ASSOC)) {
            $row[] = $results;
        }
        if(empty($row))
            return [];
        
        return ($single)? $row[0] : $row;
    }
    
    public function insert($table, $ticks = '`')
    {
        $this->sql = [];
        $this->sql[] = "INSERT INTO {$ticks}{$this->stripTableName($table)}{$ticks}";
        return $this;
    }
    
    public function columns($columns, $ticks = '`')
    {
        $this->sql[] = "({$ticks}".implode("{$ticks}, {$ticks}", $columns)."{$ticks})";
        return $this;
    }
    
    public function values($rows)
    {
        $this->bind = [];
        $this->sql[] = "VALUES";
        foreach($rows as $row) {
            $this->bind = array_merge($this->bind, $row);
            $cols[] = "(".implode(', ',array_fill(0,count($row), '?')).")";
        }
        
        if(!empty($cols))
            $this->sql[] = implode(','.PHP_EOL, $cols);
        
        return $this;
    }
    
    public function write(\PDO $db = null)
    {
        $this->stmt = implode(PHP_EOL, $this->sql);
        
        $this->query($this->stmt, $this->bind, $db);
    }
    
    public function select($columns = '*')
    {
        $this->bind =
        $this->sql = [];
        $this->sql[] = "SELECT";
        
        if(is_array($columns))
            $columns = implode(', ', $columns);
        
        $this->sql[] = $columns;
        
        return $this;
    }
    
    public function from($table)
    {
        if(is_array($table))
            $table = implode(', ', array_map(function($v){ return $this->stripTableName($v); }, $table));
        
        $this->sql[] = 'FROM '.$this->stripTableName($table);
        
        return $this;
    }
    
    public function where($where, $bind = false)
    {
        if(empty($this->bind))
            $this->bind = [];
        $this->sql[] = 'WHERE';
        if(!is_array($where)) {
            $this->sql[] = $where;
            if(!empty($bind))
                $this->bind = (is_array($bind))? array_merge($this->bind, $bind) : $bind;
            return $this;
        }
        
        foreach($where as $cond) {
            $column = (!empty($cond['c']))? $cond['c'] : false;
            $value = (!empty($cond['v']))? $cond['v'] : false;
            $operand = (!empty($cond['op']))? $cond['op'] : '=';
            $condition = (!empty($cond['co']))? $cond['co'] : '';
            $this->bind[] = $value;
            $this->sql[] = $column.' '.$operand.' ? '.$condition;
        }
        
        return $this;
    }
    
    public function orderBy($array)
    {
        $this->sql[] = 'ORDER BY';
        
        if(!is_array($array)) {
            $this->sql[] = $array;
            return $this;
        }
        
        $rows = [];
        foreach($array as $key => $value) {
            $rows[] = $key.' '.$value;
        }
        
        $this->sql[] = implode(', ', $rows);
        
        return $this;
    }
    
    public function addStmt($sql)
    {
        $this->sql[] = $sql;
        return $this;
    }
    
    public function update($table)
    {
        $this->bind =
        $this->sql = [];
        $this->sql[] = "UPDATE {$this->stripTableName($table)}";

        return $this;
    }
    
    public function set($array, $tick = "`")
    {
        $set = [];
        $this->sql[] = "SET";
        foreach($array as $key => $value) {
            $this->bind[] = $value;
            $set[] = "{$tick}{$key}{$tick} = ?";
        }
        $this->sql[] = implode(', ', $set);
        return $this;
    }
    
    public function getTables()
    {
        return array_map(function($v){
            return $v['Tables_in_'.base64_decode(DB_NAME)];
        },$this->query("show tables")->getResults());
    }
    
    public function describe($table, $tick = '`')
    {
        return $this->query("describe {$tick}{$this->stripTableName($table)}{$tick}")->getResults();
    }
    
    public function getColumnsInTable($table, $ticks = '`')
    {
        return array_map(function($v){
            return $v['Field'];
        }, $this->describe($this->stripTableName($table), $ticks));
    }
    
    public function delete($table, $ticks = '`')
    {
        $this->bind = 
        $this->sql = [];
        $this->sql[] = "DELETE FROM {$ticks}{$this->stripTableName($table)}{$ticks}";
        
        return $this;
    }
    
    public function filterArrayByColumns($table, &$array, $ticks = '`')
    {
        ArrayWorks::filterByComparison($this->getColumnsInTable($table, $ticks), $array);
    }
    
    public function fetch($one = false, \PDO $pdo = null)
    {
        $sql = implode(PHP_EOL, $this->sql);
        $bind = (!empty($this->bind))? $this->bind : null;
        try {
            $data = $this->query(implode(PHP_EOL, $this->sql), $bind, $pdo)->getResults($one);
            return $data;
        }
        catch (\PDOException $e) {
            $this->toError($e->getMessage().'stmt: '.$sql, 'sql');
        }
    }
	/**
	 *	@description	
	 */
	public function stripTableName(string $table)
	{
        $table  =   preg_replace('/[^A-Z\_\-\.\d]/i', '', strip_tags($table));
        if(empty($table))
            throw new \Exception("Invalid sql request", 500);
        
        return $table;
	}
}
