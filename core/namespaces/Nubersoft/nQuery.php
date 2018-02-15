<?php
namespace Nubersoft;

class nQuery extends \Nubersoft\ConstructMySQL
{
	public	function insertInto($table)
	{
		$this->bind		=
		$this->sql		=	array();
		$this->sql[]	=	'INSERT INTO';
		$this->sql[]	=	$this->safe()->encodeSingle($table);

		return $this;
	}

	public	function selectFrom($table,$arg=false)
	{
		$this->sql[]	=	'SELECT';

		if(is_array($arg))
			$this->sql[]	=	implode(',',$arg);
		elseif(is_string($arg))
			$this->sql[]	=	$arg;
		else
			$this->sql[]	=	'*';

		return $this;
	}

	public	function columns($arg)
	{
		if(is_array($arg)) {
			$thisObj	=	$this;
			$array[]	=	implode(',',array_map(function($v) use ($thisObj){ return $thisObj->safe()->encodeSingle($v); },$arg));
		}
		elseif(is_string($arg))
			$array[]	=	array($arg);

		$this->sql[]	=	'('.implode(', ',$arg).')';

		return $this;
	}

	public	function values($array)
	{
		$this->sql[]	=	'VALUES';

		foreach($array as $row) {
			foreach($row as $value) {
				$rSql[]			=	'?';
				$this->bind[]	=	$value;
			}
			$rowSql[]	=	'('.implode(', ',$rSql).')';
			$rSql		=	array();
		}

		$this->sql[]	=	implode(','.PHP_EOL,$rowSql);

		return $this;
	}

	public	function getBind()
	{
		return $this->bind;
	}
}