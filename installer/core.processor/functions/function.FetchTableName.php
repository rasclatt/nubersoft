<?php
	function FetchTableName($table = false)
		{
			register_use(__FUNCTION__);
			$table_id	=	(!is_numeric($table));
			
			if($table_id)
				return $table;
				
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			$data		=	$nubquery	->select("table_name")
										->from("routing_table")
										->where(array("table_id"=>$table))
										->fetch();
			
			if($data != 0)
				return $data[0]['table_name'];
			else {
					AutoloadFunction('is_admin');
					if(is_admin()) {
							exit;
							AutoloadFunction('CreateRoutingTable');
							return CreateRoutingTable($table);
						}
				}
				
			return 'users';
		}