<?php

	// Generate a tree from base SQL
	function tree_structure_by_id($info, $parent = 0)
		{    
			register_use(__FUNCTION__);
			foreach ($info as $row) {
					$row['parent_id']	=	(isset($row['parent_id']))? $row['parent_id']:'';
					if ($row['parent_id'] == $parent)
						$struc[$row['ID']] = tree_structure_by_id($info, $row['ID']);
				}
	
			$struc	=	(!empty($struc))? $struc: '';
			
			return $struc;        
		}