<?php
namespace Nubersoft;
/**
 *	@description	Create a paginated data return based on mysql-based attributes
 */
class Pagination
{
    use \Nubersoft\nQuery\enMasse;
    
    private $data, $table, $limit, $page, $sql, $statement, $counter, $bind, $order;
    # Set the page spread for ease. Modify using ->setSpread() method
    private $spread =   2;
    private $max_range  =   [20,50,100,500];
	/**
	 *	@description	Set the basic attributes to search
	 */
	public function __construct($table, $page = 1, $limit = 10, $select = '*')
	{
        $this->table    =   $this->stripTableName((is_array($table))? implode(',', $table) : $table);
        $this->limit    =   ($limit > 0)? $limit : 10;
        $this->page     =   ($page > 0)? $page : 1;
        
        if(is_array($select))
            $select =   implode(",", $select);
        
        
        $this->statement   =   (is_callable($select))? $select($this->table) : "SELECT {$select} FROM ".$this->table;
        $this->counter  =  (is_callable($select))? $select($this->table, 'count') : "SELECT COUNT(*) as count FROM ".$this->table;
	}
	/**
	 *	@description	Allow ordering
	 */
	public function orderBy($column, $type): object
	{
        $this->order    =   [];
        
        if(is_array($column)) {
            foreach($column as $k => $col) {
                if(is_array($type)) {
                    $this->order[]    =  "{$col} ".((isset($type[$k]))? $type[$k] : end($type));
                }
                else {
                    $this->order[]    =  "{$col} {$type}";
                }
            }
        }
        else {
            $this->order[]    =  "{$column} {$type}";
        }
        
        $this->order    =   " ORDER BY ".implode(', ', $this->order);
        
        return $this;
	}
	/**
	 *	@description	Set the amount of pages before and after to veiw (if row counts > 0)
	 */
	public function setSpread($spread): object
	{
        $this->spread   =   $spread;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function setMaxRange(array $range): object
	{
        $this->max_range    =   $range;
        return $this;
	}
	/**
	 *	@description	Create a WHERE clause based on a search string
	 */
	public function search($value, $key, $op = '=', $cont = "OR"): object
	{
        if(is_callable($key)) {
            $where  =   " WHERE ".$key($value, $op, $cont, $this);
        }
        else {
            if(is_array($key)) {
                $where    =   " WHERE ".implode(" {$cont} ".PHP_EOL, array_map(function($v) use ($op, $value) {
                    if(!empty($value)) {
                        $this->bind[]   =   $value;
                        return " {$v} {$op} ?";
                    }
                    else {
                        return " {$v} {$op} ''";
                    }
                }, $key));
            }
            else {
                if(!empty($value)) {
                    $this->bind[]   =   $value;
                    $where  = " WHERE {$key} {$op} ?";
                }
                else {
                    $where  = " WHERE {$key} {$op} ''";
                }
            }
        }
        
        $this->counter  .=  $where;
        $this->statement   .= $where;
        
        return $this;
	}
	/**
	 *	@description	
	 */
	public function getStatement($type = false): string
	{
        return ($type)? $this->counter : $this->statement;
	}
	/**
	 *	@description	Fetch the results OR the final SQL statement
     *  @param  [void|true|callable]    When empty, fetch pagination data.
     *  Any value other than callable will return sql statement. Callable allows extra processing of results
	 */
	public function get()
	{
        $args   =   func_get_args();
        # See how many total requested
        $count  =   $this->query($this->counter, $this->bind)->getResults(1)['count'];
        # See how many total pages are found
        $pages  =   ceil($count / $this->limit);
        # If the pages are less than the requested start point, set to max pages count
        if($this->page > $pages)
            $this->page =   $pages;
        # Determine start and end prev/next page ranges
        $start  =   $this->page - $this->spread;
        $end    =   $this->spread + $this->page;
        $offset =   ($this->limit*($this->page - 1));
        # If the offset is negative or 0, just don't put offset
        $offset =   ($offset < 0)? false : " OFFSET {$offset}";
        # Create the results query
        $this->statement .= " {$this->order} LIMIT {$this->limit} {$offset}";
        # Allow query to be returned
        if(!empty($args[0]) && !is_callable($args[0]))
            return $this;
        # Create the next/previous ranges
        for($i = $start; $i <= $end; $i++) {
            if(($i > 0) && ($i <= $pages))
                $range[]    =   $i;
        }
        # Fetch the final results
        $results    =   $this->query($this->getStatement(), $this->bind)->getResults();
        # Set the miminum page based on the result count
        if($this->page == 0 && $count > 0)
            $this->page =   1;
        # Set the minimum prev based on returned rows
        $prevdef    =   ($count == 0)? 0 : 1;
        # Set the minimum spread based on returned rows
        $spreaddef  =   ($count == 0)? [] : [1];
        # Store data
        $data   =   [
            # The per-page range
            'max_range' => $this->max_range,
            # How many rows to show per page
            'per_page' => $this->limit,
            # The range of pages before and/or after the current page
            'spread' => (!empty($range))? $range : $spreaddef,
            # The previous page or 1 if no pages
            'prev' => (($this->page - 1) >= 1)? $this->page - 1 : $prevdef,
            # The current page
            'current' => $this->page,
            # The next page or 1/current if no pages after
            'next' => (($this->page + 1) < $pages)? $this->page + 1 : $this->page,
            # Total pages available
            'total_pages' => ($pages == 0 && $count > 0)? 1 : $pages,
            # Total rows found
            'total_rows' => $count,
            # The paginated rows
            'results' => (!empty($args[0]) && is_callable($args[0]))? array_map($args[0], $results) : $results
        ];
        return $data;
	}
	/**
	 *	@description	
	 */
	public function getColumnsInTable()
	{
        $data   =   $this->query("describe ".$this->table)->getResults();
        if(empty($data))
            return ['ID'];
        $array  =   [];
        ArrayWorks::extractAll(array_map(function($v){
            return $v['Field'];
        }, $data), $array);
        
        return $array;
	}
	/**
	 *	@description	
	 */
	public function addAttr($string)
	{
        $this->counter  .=  $string;
        $this->statement   .= $string;
        return $this;
	}
	/**
	 *	@description	
	 */
	public function addToBind($value)
	{
        $this->bind[]   =   $value;
        return $this;
	}
}