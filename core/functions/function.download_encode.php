<?php
	function download_encode($settings = false, $salt = false)
		{
			// ID, not unique_id
			$file_id	=	(!empty($settings['file_id']))? $settings['file_id'] : false;
			// Table name, not id (numeric): ie. "image_bucket"
			$table_id	=	(!empty($settings['table_id']))? $settings['table_id'] : false;
			
			if(empty($file_id) || empty($table_id))
				return false;
			
			return Safe::encOpenSSL($file_id."/".$table_id);
		}