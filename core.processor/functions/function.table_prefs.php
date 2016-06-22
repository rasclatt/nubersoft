<?php
	function table_prefs($key = false)
		{
			$reg	=	nApp::getRegistry();
			
			if(empty($reg['admintoolstables']))
				return false;
			
			$tables	=	array_keys($reg['admintoolstables']);
			
			if(!empty($key)) {
				if(!in_array($key,$tables))
					return false;
			}
			
			$validTables	=	(array) nApp::getTables();
			
			foreach($reg['admintoolstables'] as $tablename => $val) {
				if(empty($val['col']))
					continue;
				elseif(!in_array($tablename,$validTables))
					continue;
				
				$new[$tablename]	=	(!is_array($val['col']))? array($val['col']) : $val['col'];
			}
			
			return $new;
		}