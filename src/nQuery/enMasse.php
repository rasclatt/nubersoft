<?php
namespace Nubersoft\nQuery;

trait enMasse
{
	protected static $nQuery;
	
	public	function query($sql, $bind = false, $conn = false)
	{
		return $this->nQuery()->query($sql, $bind, $conn);
	}
	
	public	function getColumnsInTable($table,$ticks = '`')
	{
		return $this->nQuery()->{__FUNCTION__}($table, $ticks);
	}
	
	public	function nQuery()
	{
		if(empty(self::$nQuery))
			self::$nQuery	=	new \Nubersoft\nQuery();
		
		return self::$nQuery;
	}
}