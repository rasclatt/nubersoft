<?php
/*
*	Copyright (c) 2017 Nubersoft.com
*	Permission is hereby granted, free of charge *(see acception below in reference to
*	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
*	and associated documentation files (the "Software"), to deal in the Software without
*	restriction, including without limitation the rights to use, copy, modify, merge, publish,
*	or distribute copies of the Software, and to permit persons to whom the Software is
*	furnished to do so, subject to the following conditions:
*	
*	The base CMS software* is not used for commercial sales except with expressed permission.
*	A licensing fee or waiver is required to run software in a commercial setting using
*	the base CMS software.
*	
*	*Base CMS software is defined as running the default software package as found in this
*	repository in the index.php page. This includes use of any of the nAutomator with the
*	default/modified/exended xml versions workflow/blockflows/actions.
*	
*	The above copyright notice and this permission notice shall be included in all
*	copies or substantial portions of the Software.
*
*	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*	SOFTWARE.

*SNIPPETS:*
*	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
*	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
*	NOT BEEN LOCATED AND DELETED.
*/
namespace Nubersoft;

class ConstructMySQL extends \Nubersoft\nApp implements \Nubersoft\QueryEngine
{
	protected	$bind,
				$query,
				$table,
				$Safe,
				$is_query;

	private		$fetched_data;

	protected	$ticks	=	true;

	protected	static	$con,
						$queries,
						$alpha,
						$bindArr,
						$storeQueries;

	const	RESET_CONNECTION	=	true;

	public	function __construct($dbObject = false)
	{
		if($dbObject instanceof \PDO)
			self::$con	=	$dbObject;

		if(!(self::$con instanceof \PDO))
			$this->getConnection();

		if(!isset(self::$queries))
			self::$queries	=	0;
		
		return parent::__construct();
	}
	/*
	*	@description	Returns the database connection
	*/
	public	function getConnection()
	{
		if(!(self::$con instanceof \PDO))
			self::$con	=	$this->getHelper('DatabaseConfig')->connect();

		if(empty(self::$con))
			throw new \Exception($this->__("Database configuration invalid."),89523);
		
		return self::$con;
	}

	public	function getQuery()
	{
		return $this->query;
	}

	public	function execute()
	{
		echo printpre();
	}

	protected	function recordQuery($sql)
	{
		# Store the query refrence. Helps keep track of duplicates
		$store	=	md5($sql);
		# If there is a unique query store it 
		if(!is_array(self::$storeQueries))
			self::$storeQueries	=	array();
		# Store query values
		self::$storeQueries[]	=	$store;
		$this->bind		=
		# reset bind
		self::$bindArr	=	false;
		self::$queries	+=	1;
	}

	public	function sendQuery($sql)
	{
		try {
			if(!empty($this->getBind()) || !empty($this->bind)) {
				# Get the general bind array
				$bind	=	(!empty($this->getBind()))? $this->getBind() : $this->bind;
				# Isolate the query bind array
				$qBind	=	$this->getBind('query');
				# Fetch the PDO connetion
				$con	=	$this->getConnection();
				# Prepare
				$query	=	$con->prepare($sql);
				# See if there is a general query bind array
				if(!empty($qBind)) {
					foreach($qBind as $key => $value) {
						$key		=	str_replace(':query',':',$key);
						$bindArr[$key]	=	$value;
					}
				}
				else
					$bindArr	=	$bind;

				if(!is_array($bindArr))
					die(printpre(self::$bindArr));

				if($query->execute($bindArr)) {
					# Set the query
					$this->query		=	$query;
					$this->recordQuery($sql);
				}
			}
			else {
				$query			=	$this->getConnection()->query($sql);
				$this->query	=	$query;
				$this->bind		=
				# reset bind
				self::$bindArr	=	false;
				self::$queries	+=	1;
			}
		}
		catch(\PDOException $e) {
			if($this->isAdmin()) {
				if(strpos($e->getMessage(),'exist') !== false && strpos($e->getMessage(),'Table') !== false){
					if(is_file($inc = NBR_ROOT_DIR.DS.'installer'.DS.'support'.DS.'table_data'.DS.'sql.php')) {
						$dbname	=	$this->getDbName();
						preg_match('/\'([a-zA-Z0-9\_\-\.]{1,})\'/',$e->getMessage(),$match);

						if(isset($match[1])) {
							include($inc);
							$table	=	str_replace($dbname.'.','',$match[1]);
							if(!empty($data[$table])) {
								$this->getConnection()->query($data[$table]);
							}
						}
					}
				}

				$this->saveError('database',array('msg'=>$e->getMessage().' SQL: '.$sql.strip_tags(printpre())));
			}
			else {
				$this->saveError('database',array('msg'=>$e->getMessage()));
				return false;
			}
		}
	}

	public	function useAlpha($i)
	{
		if(!is_array(self::$alpha))
			self::$alpha	=	str_split('abcdefghijklmnopkrstuvwxyz');

		return self::$alpha[$i];
	}

	public	function write()
	{
		$this->sendQuery($this->getStatement());
		if(self::RESET_CONNECTION)
			self::$con	=	NULL;
	}

	public	function standardBind($array)
	{
		$array	=	$this->toBind($array,'bind');
		return $array['array'];
	}

	public	function query($sql,$bind = false)
	{
		$this->sql	=	array($sql);

		try {
			if(!empty($bind)) {
				if(!is_array($bind))
					$bind	=	array($bind);

				$bindArr		=	$this->standardBind($bind);
				$con			=	$this->getConnection();
				$this->query	=	$con->prepare($sql);
				$this->query->execute($bindArr);
				$this->fetched_data	=	false;
			}
			else {
				$con				=	$this->getConnection();
				$this->query		=	$con->query($sql);
				$this->fetched_data	=	false;
			}

			$this->recordQuery($sql);
		}
		catch(\PDOException $e) {
				$msg	=	$e->getMessage();
				$this->saveToLogFile('..'.DS.'sql_query'.DS.date('Y-m-d_H-i-s').'_'.mt_rand().'_query.log',$msg);

			if($this->isAdmin())
				$this->saveError('database',array('msg'=>$msg.' SQL: '.$sql.' '.strip_tags(printpre())));
			else
				$this->saveError('database',array('msg'=>$msg));
		}

		return $this;
	}

	public	function toNode($results = false)
	{
		$rows		=	$this->getResults($results);
		$Methodize	=	new Methodize();
		$Methodize->saveAttr('data_node',$rows);
		return $Methodize->getDataNode();
	}

	public	function getResults($limit = false, $key = false)
	{
		if(empty($this->fetched_data)) {

			if(empty($this->getQuery())) {
				trigger_error('Your prepare/query has failed. Check your sql statement.',E_USER_NOTICE);

				return 0;
			}

			while($row = $this->getQuery()->fetch(\PDO::FETCH_ASSOC)) {
				$result[]	=	$row;
			}
		}
		else {
			$result	=	$this->fetched_data;
		}
		if(self::RESET_CONNECTION)
			self::$con		=	NULL;

		$queryResults	=	(!empty($result))? $result : 0;

		if($queryResults == 0)
			return $queryResults;

		if($limit)
			return $queryResults[0];

		if($key) {
			return $this->organizeByKey($queryResults,$key,array('unset'=>false));
		}

		return	$queryResults;
	}

	public	function fetch($limit = false, $key = false)
	{
		if(count($this->sql) >= 1) {
			$statement	=	$this->getStatement();
			$this->sendQuery($statement);
		}
		
		# Reset the sql so the sql doesn't persist
		$this->resetAttr();

		if(empty($this->getQuery())) {
			if(self::RESET_CONNECTION)
				self::$con	=	NULL;
			return 0;
		}

		while($row = $this->getQuery()->fetch(\PDO::FETCH_ASSOC)) {
			$result[]	=	$row;
		}
		
		
		$queryResults	=	(!empty($result))? $result : 0;
		if(self::RESET_CONNECTION)
			self::$con	=	NULL;

		if($queryResults == 0)
			return $queryResults;

		if($limit)
			return $queryResults[0];

		if($key) {
			return $this->organizeByKey($queryResults,$key,array('unset'=>false));
		}

		return	$queryResults;
	}

	public	function getQueryCount()
	{
		return self::$queries;
	}

	public	function select($values = array(),$distinct = false)
	{
		$this->resetAttr();
		$useTicks	=	(!empty($this->ticks))? '`' : '';

		if(is_array($values))
			$values		=	(!empty($values))? $useTicks.implode($useTicks.", {$useTicks}",$values).$useTicks:"*";

		$this->sql[]	=	"SELECT";

		if($distinct)
			$this->sql[]	=	"DISTINCT";

		$this->sql[]	=	$values;

		return $this;
	}

	public	function from($table = false)
	{
		$useTicks	=	(!empty($this->ticks))? '`' : '';
		$this->sql[]		=	"FROM";

		if(is_array($table)) {
			if(count($table) > 1) {
				foreach($table as $key => $value) {
					if(is_numeric($key))
						$key	=	$this->useAlpha($key);

					if(is_array($value)) {
						foreach($value as $append) {
							$tables[]	=	"{$key}{$useTicks}.{$useTicks}{$append}";
						}
					}
					else
						$tables[]	=	"{$key}{$useTicks}.{$useTicks}{$value}";
				}
			}
			else
				$tables	=	$table;

			$table	=	$useTicks.implode("{$useTicks}, {$useTicks}",$tables).$useTicks;
		}
		else
			$table	=	"{$useTicks}{$table}{$useTicks}";

		$this->sql[]	=	$table;

		return $this;
	}

	public	function where()
	{
		$args			=	func_get_args();
		$columns_values	=	$args[0];
		$operand		=	(empty($args[1]))? ' AND ' : ' '.$args[1].' ';
		$not			=	(isset($args[2]))? $args[2] : false;
		$isolate		=	(isset($args[3]))? $args[3] : false;
		$skipWhere		=	(isset($args[4]))? $args[4] : false;
		
		if(!$skipWhere)
			$this->sql[]		=	'WHERE';

		$equals				=	(empty($not))? "=":"!=";
		if(is_array($columns_values)) {
			$brackets['f']	=	($isolate)? '(' : '';
			$brackets['e']	=	($isolate)? ')' : '';
			
			$bindWhere	=	$this->toBind($columns_values,'bind','where');
			
			if(!is_array($this->bind))
				$this->bind	=	[];
			
			$this->bind		=	array_merge($this->bind,$bindWhere['array']);
			
			# Create where string
			$this->sql[]	=	$brackets['f'].implode($operand,$bindWhere['strings']).$brackets['e'];
		}
		else
			$this->sql[]	=	$brackets['f'].$columns_values.$brackets['e'];
		
		return $this;

	}
	
	public	function useTicks($use = true)
	{
		$this->ticks	=	$use;
		return $this;
	}
	/**
	*	@description	Sets all the tables
	*/
	public	function fetchTablesInDB($database = false)
	{
		$database			=	(!empty($database))? $database : $this->getHelper('FetchCreds')->getData();
		$query				=	$this->getConnection()->query("show tables in `".$database."`");
		while($result = $query->fetch(\PDO::FETCH_ASSOC)) {
			$this->fetched_data[]	=	$result;
		}
		return $this;
	}

	public	function getStatement()
	{
		return implode(' ',$this->sql);
	}

	public	function getTicks()
	{
		return  (!empty($this->ticks))? '`' : '';
	}

	public	function whereIn($col = false, $values = array())
	{
		$this->sql[]	=	"WHERE ".$this->getTicks().$col.$this->getTicks()." IN";

		if(is_array($values) && !empty($values)) {

			$this->setBind($values,'wherein');

			$this->sql[]	=	"(".implode(",",array_keys($this->getBind('wherein'))).")";
		}
		else {
			$this->sql[]		=	$values;
		}

		return $this;
	}
	
	public	function whereNot($columns_values, $operand = 'AND',$isolate = false,$skipWhere=false)
	{
		$this->where($columns_values,$operand,true,$isolate,$skipWhere);
		
		return $this;
	}

	public	function like($values = array('like'=>'','columns'=>array()), $match = false, $isolate = false)
	{
		$this->sql[]	=	'WHERE';

		if($isolate)
			$this->sql[]	=	'(';

		if(is_array($values['columns'])) {
			foreach($values['columns'] as $key) {
				$key	=	trim($key,":");
				if($match == 'starts')	
					$this->addBind('like',trim($values['like']."%"));
				elseif($match == 'ends')
					$this->addBind('like',trim("%".$values['like']));
				else
					$this->addBind('like',trim("%".$values['like']."%"));

				$allCols[]	=	$key;
			}

			$final	=	array_combine($allCols,array_keys($this->getBind('like')));

			foreach($final as $key => $value) {
				$combine[]	=	$this->getTicks().$key.$this->getTicks()." like :".ltrim($value,':');
			}

			$this->sql[]	=	implode(" or ",$combine);

			if($isolate)
				$this->sql[]	=	')';
		}
		else {
			if(!empty($values))
				$this->sql[]		=	$values;
		}

		return $this;
	}

	public	function limit($value = false,$offset = false)
	{
		if(is_numeric($value)) {
			$limit	=	$value;
			$this->sql[]	=	'LIMIT';
			$this->sql[]	=	(is_numeric($offset))? $offset.", ".$limit : $limit;
		}

		return $this;
	}

	public	function orderBy($column = array())
	{
		$useTicks	=	(!empty($this->ticks))? '`' : '';

		if(is_array($column) && !empty($column)) {
			foreach($column as $colname => $orderby) {
				$array[]	=	$useTicks.$colname.$useTicks." ".str_replace(array("'",'"',"+",";"),"",$orderby);
			}
		}
		else
			$array[]	=	$column;

		$this->sql[]	=	'ORDER BY';
		$this->sql[]	=	implode(", ",$array);

		return $this;
	}

	public	function delete()
	{
		$this->resetAttr();

		if(!in_array("DELETE",$this->sql))
			$this->sql[]	=	"DELETE";

		return $this;
	}

	public	function insert($value = "")
	{
		// Reset all values
		$this->resetAttr();
		$this->sql[]		=	"INSERT INTO ".$this->getTicks().$value.$this->getTicks();
		return $this;
	}

	public	function update($value = "")
	{
		// Reset all values
		$this->resetAttr();
		$this->sql[]	=	"UPDATE ".$this->getTicks().$value.$this->getTicks();
		return $this;
	}

	public	function set($columns_values = "",$prefix=false)
	{
		$this->sql[]	=	"SET";

		if(is_array($columns_values)) {
			
			$setBind	=	$this->toBind($columns_values,'bind','set'.$prefix);
			
			if(!is_array($this->bind))
				$this->bind	=	[];

			$this->bind		=	array_merge($this->bind,$setBind['array']);
			$this->sql[]	=	implode(',',$setBind['strings']);
		}
		else
			$this->sql[]	=	$columns_values;

		return $this;
	}

	public	function alter($table = false)
	{
		$this->resetAttr();
		$this->sql[]	=	"ALTER TABLE";
		$this->sql[]	=	$this->getTicks().$table.$this->getTicks();

		return $this;
	}

	public	function columnsValues($rows,$forceblank = true)
	{
		$this->bind	=	[];
		$cols		=	false;
		foreach($rows as $key => $value) {
			
			if(!$forceblank)
				$value	=	array_filter($value);
			
			if(!$cols) {
				$this->sql[]	=	'('.$this->getTicks().implode($this->getTicks().', '.$this->getTicks(),array_keys($value)).$this->getTicks().')';
				$this->sql[]	=	'VALUES';
				$cols	=	true;
			}
			# Fetch bind
			$getBind		=	$this->toBind($value,'bind',$key);
			$this->bind		=	array_merge($this->bind,$getBind['array']);
			# Store to string
			$setColsVals[]	=	'('.implode(', ',$getBind['keys']).')';
		}
		$this->sql[]	=	implode(', ', $setColsVals);

		return $this;
	}

	public function	describe($table = false)
	{
		// Reset all values
		$this->resetAttr();
		$this->query("DESCRIBE ".$this->getTicks().$table.$this->getTicks());
		return $this;
	}
	
	public	function addCustom($value = false,$reset = false)
	{
		if($reset)
			$this->resetAttr();

		$this->sql[]	=	$value;

		return $this;
	}

	public	function resetAttr()
	{
		$this->fetched_data	=	false;
		$this->sql			=	
		$this->bind			=
		self::$bindArr		=	array();
	}

	public	function addBind($type, $value)
	{
		$count	=	 $this->countBind($type);
		$sKey	=	":{$type}_{$count}";
		self::$bindArr[$type][$sKey]	=	$value;
	}

	public	function setBind($bind,$type = 'sql')
	{

		if(!is_array($bind))
			return;

		$count	=	$this->countBind($type);

		foreach($bind as $key => $value) {
			$sKey	=	":{$type}{$count}";
			self::$bindArr[$type][$sKey]	=	$value;
			$count++;
		}
		
		return $this;
	}

	protected	function countBind($type)
	{
		return (isset(self::$bindArr[$type]) && is_array(self::$bindArr[$type]))? count(self::$bindArr[$type]) : 0;
	}

	public	function getBind($type = false,$raw=false)
	{
		if(!empty($type))
			return (!empty(self::$bindArr[$type]))? self::$bindArr[$type] : false;

		if(!$raw) {
			if(!empty(self::$bindArr)) {
				$new	=	array();
				foreach(self::$bindArr as $kind => $group) {
					$new	=	array_merge($new,$group);
				}

				return $new;
			}
		}
		return (!empty(self::$bindArr))? self::$bindArr : false;
	}

	public	function getInsertColumnsFromBind()
	{
		if(!is_array($this->bind))
			return false;

		$keys	=	array_keys($this->bind);

		return	'('.implode(', ',$keys).')';
	}

	public	function getUpdateFromBind($array,$bind_with = 'AND',$operand = '=')
	{
		if(!is_array(self::$bindArr['set']))
			return false;

		$useTicks	=	$this->getTicks();
		$values		=	array_keys(self::$bindArr['set']);
		$keys		=	array_keys($array);
		$where		=	array_combine($keys,$values);

		foreach($where as $key => $value) {
			$new[]	=	"{$useTicks}{$key}{$useTicks} {$operand} {$value}";	
		}

		return (!empty($new))? implode(" {$bind_with} ",$new) : '';
	}

	public	function getEqualsFromBind($columns,$type,$bind_with = 'AND',$operand = '=')
	{
		$bindArray	=	$this->getBind($type);
		$useTicks	=	$this->getTicks();
		if(empty($bindArray))
			return false;

		$values	=	array_keys($bindArray);
		$where	=	array_combine($columns,$values);

		foreach($where as $key => $value) {
			$new[]	=	"{$useTicks}{$key}{$useTicks} {$operand} {$value}";	
		}
		return (!empty($new))? implode(" {$bind_with} ",$new) : '';
	}
	/**
	*	@description	Creates an anonymous or named bind array
	*/
	public	function toBind($data,$return='bind',$prefix=false)
	{
		if(!is_array($data)) {
			trigger_error('"$data" must be array.',E_USER_NOTICE);
			return false;
		}
		
		foreach($data as $key => $value) {
			$key			=	ltrim($key,':');
			$qs[]			=	'?';
			$bKey			=	":{$prefix}".trim($key,'`');
			$bind[$bKey]	=	$value;
			$update[]		=	"{$key} = {$bKey}";
			$qUpdate[]		=	"{$key} = ?";
		}
		
		$data		=	[
			'bind' => [
				'array' => $bind,
				'keys' => array_keys($bind),
				'strings' => $update
			],
			'anon' => [
				'values' => array_values($data),
				'marks' => $qs,
				'strings' => $qUpdate
			]
		];

		if(empty($return))
			return $data;
		else
			return (isset($data[$return]))? $data[$return] : false;
	}
	/**
	*	@description	Select from table
	*/
	public	function selectFrom($table,$where=false,$columns=false,$settings=false)
	{
		$sort		=	(!empty($settings['sort']))? $settings['sort'] : false;
		$operand	=	(!empty($settings['operand']))? $settings['operand'] : 'AND';
		$statement	=	(!empty($settings['statement']));
		$assoc		=	(!empty($settings['assoc']));
		
		$columns	=	(is_array($columns))? implode(',',$columns) : $columns;
		if(empty($columns))
			$columns	=	'*';
		$operand	=	(empty($operand))? 'AND' : $operand;
		$orderby	=
		$whereSQL	=
		$bind		=	false;
		
		if(!empty($sort)) {
			$orderby['start']	= ' ORDER BY ';
			if(is_array($sort)) {
				foreach($sort as $col => $ord) {
					$orderby['attr'][]	=	"$col $ord";
				}
				
				$orderby['attr']	=	implode(', ',$orderby['attr']);
			}
			else
				$orderby['attr']	=	$sort;
			
			$orderby	=	implode(' ',$orderby);
		}
		
		if(!empty($where)) {
			$whereSQL	=	" WHERE ";
			if(is_array($where)) {
				if(isset($where['NOTEQUAL'])) {
					$data		=	$this->toBind($where['NOTEQUAL'],'bind','wherenotequal');
					$bind		=	(!empty($data['array']))? $data['array'] : false;
					if(!empty($data['strings']))
						$whereSQL	.=	implode(" {$operand} ",str_replace(' = ',' != ',$data['strings']));
					
					if(isset($where['EQUALS'])) {
						$data		=	$this->toBind($where['EQUALS'],'bind','where');
						if(!empty($data['array'])) {
							if(!empty($bind))
								$bind	=	array_merge($bind,$data['array']);
							else
								$bind	=	$data['array'];
						}
						
						if(!empty($data['strings']))
							$whereSQL	.=	implode(" {$operand} ",$data['strings']);
					}
				}
				else {
					$data		=	$this->toBind($where,'bind','where');
					$bind		=	(!empty($data['array']))? $data['array'] : false;
				
					if(!empty($data['strings']))
						$whereSQL	.=	implode(" {$operand} ",$data['strings']);
				}
			}
			else {
				$whereSQL	.=	' '.$where;
			}
		}
		
		$sql	=	"SELECT {$columns} FROM {$table}{$whereSQL}{$orderby}";
		
		return ($statement)? [ 'statement'=>$sql,'bind'=>$bind ] : $this->query($sql,$bind)->getResults($assoc);
	}
}