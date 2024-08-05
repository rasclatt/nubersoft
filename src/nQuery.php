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
    /**
     *	@description	Execute a transaction
     *	@var	        callable $func
     */
    public function transaction(callable $func, callable $error = null)
    {
        $this->getConnection()->beginTransaction();
        try {
            $func($this);
            $this->getConnection()->commit();
        }
        catch(\PDOException $e) {
            $this->getConnection()->rollBack();
            if($error)
                $error($e);
            else
                throw $e;
        }
        return $this;
    }
    /**
     *	@description	
     *	@var	        array $select = ['u.*','other_table.ID as other_id'];
     *	@var	        array $from = ['users as u','other_table'];
     *	@var	        array $joins = [
     *                               ["type" => "INNER", "table" => "orders as o", "condition" => "u.id = o.user_id"],
     *                               ["type" => "LEFT", "table" => "payments", "condition" => "o.id = payments.order_id"]
     *                           ];
     * @var            array $where = ["u.status = 'active'", "o.amount > 100", ["condition" => "u.email = ?", "bind" => "someemail@example.com"]];
     */
    public function selectWithJoin(array $select, array $from, array $joins = [], array $where = [], array $other = []) {
        $this->bind =
        $this->sql =
        $bind = [];
        $this->stmt = "";
        $selectPart = "SELECT " . implode(", ", $select);
        $fromPart = "FROM " . implode(", ", $from);
        $joinParts = [];
        foreach ($joins as $join) {
            $joinType = $join['type'] ?? 'INNER';
            $joinTable = $join['table'];
            $joinCondition = $join['condition'];
            if(!empty($join['bind'])) {
                $bind = array_merge($bind, (!is_array($join['bind']))? [ $join['bind'] ] : $join['bind']);
            }
            $joinParts[] = "{$joinType} JOIN {$joinTable} ON ";
            if(is_array($joinCondition)) {
                $j = [];
                foreach ($joinCondition as $joinOn) {
                    if(!empty($joinOn['bind'])) {
                        $j[] = $joinOn['condition'];
                        $bind = array_merge($bind, (!is_array($joinOn['bind']))? [ $joinOn['bind'] ] : $joinOn['bind']);
                    } else {
                        $j[] = $joinOn;
                    }
                }
                $joinParts[] = implode(" AND ", $j);
            } else {
                $joinParts[] = $joinCondition;
            }
        }
        $joinPart = implode(" ", $joinParts);
        $whereParts = [];
        foreach ($where as $condition) {
            if(isset($condition['bind'])) {
                $whereParts[] = $condition['condition'];
                $bind = array_merge($bind, (!is_array($condition['bind']))? [ $condition['bind'] ] : $condition['bind']);
            } else {
                $whereParts[] = $condition;
            }
        }
        $wherePart = empty($whereParts) ? "" : " WHERE " . implode(" AND ", $whereParts);
        $this->sql = array_filter([$selectPart, $fromPart, $joinPart, $wherePart, (!empty($other))? implode(PHP_EOL, $other) : ""]);
        $this->stmt = implode(PHP_EOL, $this->sql);
        $this->bind = $bind;
        return $this->query($this->stmt, $this->bind);
    }
    /**
     *	@description	
     *	@var	
     */
    public function toJson()
    {
        return json_encode($this->getResults());
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
    
    public function softDelete($table, $ticks = '`', array $where = null, array $bind = null)
    {
        $this->bind = 
        $this->sql = [];
        $this->sql[] = "UPDATE {$ticks}{$this->stripTableName($table)}{$ticks} SET deleted_at = NOW()";
        return $this->where($where, $bind);
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
    /**
     *	@description	Returns a string with the number of ? for binding
     *	@var	        array $array    The column names to return the binding for
     *  @var            bool $braces    If true, will wrap the binding in parenthesis
     */
    public static function likeIn(array $array, bool $braces = false): string
    {
        return ($braces? '(' : '').implode(',', array_fill(0, count($array), '?')).($braces? ')' : '');
    }
    /**
     *	@description	Builds a sql string and bind array from an array. Good for updates and deletes
     *	@var	        array $array        The array to build the sql from
     *  @var            string $op          The operator to use in the sql string (default is ',' but can use 'AND' or 'OR')
     *  @var            bool $ignoreNull    If true, will ignore null values in the array
     */
    public static function build(array $array, string $op = ',', bool $ignoreNull = false): object
    {
        $sql = $bind = [];
        foreach($array as $k => $v) {
            if($v === null) {
                if(!$ignoreNull)
                    $sql[] = "`{$k}` = NULL";
            } else {
                $sql[] = "`{$k}` = ?";
                $bind[] = $v;
            }
        }
        return (object) [
            'bind' => $bind,
            'sql' => implode($op, $sql)
        ];
    }
}
