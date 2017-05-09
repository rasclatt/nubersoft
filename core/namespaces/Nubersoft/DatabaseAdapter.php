<?php
/*
**	@method	connect() Implements the connection
**			- $this->con must contain the connection itself
**	@method	useCreds() Required for the db connection
**	@method	getConnection() Returns connection
**
**	@use	DatabaseAdapter->PDOEngine->DatabaseEngine
*/
namespace Nubersoft;

abstract class	DatabaseAdapter
	{
		public	function connect()
			{
			}
		
		public	function useCreds($array)
			{
				$this->creds	=	$array;
				return $this;
			}
		
		public	function getConnection()
			{
				return (!empty($this->con))? $this->con : false;
			}
	}