<?php
/*Title: FetchTableId*/
/*Description: This function is used to lookup and create an instance in the `routing_table` table. This table allows you to mask the name of your table if using the table name in a form like `<input name="requestTable" value="<?php echo FetchTableId('users'); ?>" />`.*/

	function FetchTableId($table =  false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			$table_id	=	(is_numeric($table))? true:false;
			
			if($table_id == true) {
					$routed	=	$nubquery	->select("COUNT(*) as count")
											->from("routing_table")
											->where(array("table_id"=>$table))
											->fetch();
											
					return	($routed[0]['count'] > 0)? $table:false;
				}
			
			$data	=	$nubquery->select("table_id")->from("routing_table")->where(array("table_name"=>$table))->fetch();
			
			if($data != 0)
				return $data[0]['table_id'];
			else {
					AutoloadFunction('is_admin');
					if(is_admin()) {
							AutoloadFunction('CreateRoutingTable');
							return CreateRoutingTable($table);
						}
				}
				
			return '1';
		}