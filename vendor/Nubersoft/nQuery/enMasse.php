<?php
namespace Nubersoft\nQuery;

trait enMasse
{
	public	function query($sql,$bind = null)
	{
		return (new \Nubersoft\nQuery())->query($sql,$bind);
	}
}