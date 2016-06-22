<?php
/*Title: fetch_table_id*/
/*Description: This function is used to lookup and create an instance in the `routing_table` table. This table allows you to mask the name of your table if using the table name in a form like `<input name="requestTable" value="<?php echo fetch_table_id('users'); ?>" />`.*/

	function fetch_table_id($table =  false)
		{
			if(empty($table_id))
				return false;

			$table_id	=	(is_numeric($table));
			
			if($table_id) {
					$routed	=	nQuery()	->select("COUNT(*) as count")
											->from("routing_table")
											->where(array("table_id"=>$table))
											->fetch();
											
					return	($routed[0]['count'] > 0)? $table:false;
				}
			
			$id	=	nApp::getRoutingTables($table);
			
			if($id)
				return $id;
			else {
				if(is_admin()) {
					AutoloadFunction('CreateRoutingTable');
					return CreateRoutingTable($table);
				}
			}
			
			return false;
		}