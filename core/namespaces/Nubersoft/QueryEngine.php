<?php
/*Title: QueryEngine*/
/*Description: This class is the base interface for the sql statment & fetching/writing engine(s).*/
namespace Nubersoft;

interface QueryEngine
	{
		public	function __construct();
		/*
		public	function select($values,$distinct);
		
		public	function from($table);
		
		public	function where($array);
		
		public	function orderBy($array);
		
		public	function fetch();
		
		public	function write();
		
		public	function insert($table);
		
		public	function update($table);
		
		public	function columnsValues($columns,$values);
		
		public	function set($array);
		
		public	function getResults();
		
		public	function query($sql,$bind = false);
		*/
	}