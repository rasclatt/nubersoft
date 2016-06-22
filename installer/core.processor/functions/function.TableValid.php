<?php
	function TableValid($table = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery');
			$nubquery		=	nQuery();
			$settable		=	FetchTableName($table);
			
			return	(is_object($nubquery))? $nubquery->tableExists($settable,'request',true)->table:0;
		}
?>