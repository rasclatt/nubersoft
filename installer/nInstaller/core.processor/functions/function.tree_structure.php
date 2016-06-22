<?php

	// Generate a tree from base SQL
	function tree_structure($info, $parent = 0)
		{    
			register_use(__FUNCTION__);
			foreach ($info as $row) {
					$row['parent_id']	=	(isset($row['parent_id']))? $row['parent_id']:'';
					if ($row['parent_id'] == $parent)
						$struc[$row['unique_id']] = tree_structure($info, $row['unique_id']);
				}
	
			$struc	=	(!empty($struc))? $struc: '';
			
			return $struc;        
		}