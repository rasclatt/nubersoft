<?php
	function table_valid($table = false)
		{
			
			AutoloadFunction('nQuery');
			$nubquery		=	nQuery();
			$settable		=	fetch_table_name($table);
			
			return	(is_object($nubquery))? $nubquery->tableExists($settable,'request',true)->table:0;
		}
?>