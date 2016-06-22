<?php
	function CreateRoutingTable($table = false)
		{
			register_use(__FUNCTION__);
			if($table != false && !empty($table)) {
					AutoloadFunction('nQuery,TableIdStatus');
					$nubquery	=	nQuery();
					$table_name	=	$nubquery->describe($table)->fetch();
					
					if($table_name != 0) {
							AutoloadFunction('FetchUniqueId');
							$num	=	FetchUniqueId(rand(100,999),true);
							TableIdStatus($num,$table);
						}
				}
			
			return false;
		}
?>