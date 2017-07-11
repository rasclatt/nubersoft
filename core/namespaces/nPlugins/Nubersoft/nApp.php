<?php
namespace nPlugins\Nubersoft;

class nApp extends \Nubersoft\nApp
	{
		public	function getComponent($name,$type='ref_anchor')
			{
				return $this->nQuery()->query("select * from components where `".str_replace('`','',$type)."` = :0",array($name))->getResults(true);
			}
	}