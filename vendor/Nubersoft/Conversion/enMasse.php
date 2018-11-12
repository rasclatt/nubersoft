<?php
namespace Nubersoft\Conversion;

trait enMasse
{
	public	function colToTitle($value, $uc = true)
	{
		return (new \Nubersoft\Conversion())->columnToTitle($value, $uc);
	}
}