<?php
	function menu_organize_id($data = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('organize');
			//$data	=	organize($data,'unique_id');
			
			if(is_array($data) && !empty($data)) {
					AutoloadFunction('tree_structure');	
					return tree_structure($data);
				}
		}
?>