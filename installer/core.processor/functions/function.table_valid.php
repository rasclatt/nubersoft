<?php
	function table_valid($table = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery');
			$nubquery		=	nQuery();
			$settable		=	fetch_table_name($table);
			
			return	(is_object($nubquery))? $nubquery->tableExists($settable,'request',true)->table:0;
		}
?>