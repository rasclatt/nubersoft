<?php
	function get_file_dir($table = false,$default = '/client_assets/images/default/')
		{
			register_use(__FUNCTION__);
			$query	=	nQuery();
			$table	=	(!empty($table))? trim($table):false;
			
			if(empty($table) || empty($query))
				return $default;
			
			$dir	=	$query	->select("file_path")
								->from("upload_directory")
								->where(array("assoc_table"=>$table))
								->fetch();
			
			return ($dir != 0)? $dir[0]["file_path"]:$default;
		}
?>