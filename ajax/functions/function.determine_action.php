<?php
function determine_action($REQUEST = false)
	{
		$nubquery	=	nQuery();
		$query		=	0;
		
		if(!empty($REQUEST['duplicate'])) {
			// Check if unique_id is set
			$empty		=	(!empty($REQUEST['unique_id']));
			if(!$empty) {
				$query	=	$nubquery	->select()
										->from("components")
										->where(array("ID"=>$REQUEST['ID']))
										->fetch();
				if(!empty($query[0]))
					$query	=	array_filter($query[0]);
			}
			
			return array("action"=>'duplicate',"query"=>$query,"table"=>"components");
		}
		else {
			$table	=	Safe::decOpenSSL(urlencode($REQUEST['requestTable']));
			$query	=	(empty($REQUEST['requestTable']))? 0 : $nubquery	->select()
																			->from($table)
																			->where(array("ID"=>$REQUEST['ID']))
																			->fetch();
			$query	=	(!empty($query[0]))? $query[0] : 0;
			$action	=	'no.action';
			
			if(!empty($REQUEST['delete']))
				$action	=	'delete';
			elseif(isset($REQUEST['action']))
				$action	=	'action';
			elseif($query != 0)
				$action	=	'default';
			
			return array("action"=>$action,"query"=>$query,"table"=>$table);
		}	
	}