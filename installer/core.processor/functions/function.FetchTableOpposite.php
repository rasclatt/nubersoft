<?php
	function FetchTableOpposite($table = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('fetch_table_id,fetch_table_name');
			if(!is_numeric($table))
				return fetch_table_id($table);
	
			return fetch_table_name($table);
		}
?>