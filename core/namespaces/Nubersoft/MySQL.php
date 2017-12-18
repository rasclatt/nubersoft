<?php
namespace Nubersoft;

class MySQL extends \Nubersoft\CoreMySQL
{
	private	$columns;
	
	public	function getColumns($table)
	{
		$query		=	$this->describe($table)->getResults();

		if($query == 0)
			return false;

		$this->columns	=	array_keys(ArrayWorks::organizeByKey($query,'Field'));
		
		return $this->columns;
	}
}