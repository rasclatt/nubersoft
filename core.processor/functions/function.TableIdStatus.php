<?php
	function TableIdStatus($id, $table)
		{
			
			AutoloadFunction('nQuery,FetchUniqueId');
			$nubquery	=	nQuery();
			$nubquery	->insert("routing_table")
						->setColumns(array("unique_id","table_name","table_id","page_live"))
						->setValues(array(array(FetchUniqueId(mt_rand(1000,9000)),$table,$id,"on")))
						->write();
			
			return $id;
		}