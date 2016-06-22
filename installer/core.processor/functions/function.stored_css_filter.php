<?php
	function stored_css_filter($array = false)
		{
			register_use(__FUNCTION__);
			$_filter = array('ID','unique_id','parent_id','ref_anchor','ref_page','component_type','content','_id','a_href','login_view','login_permission','page_order','page_live','admin_tag','admin_notes','email_id','class','file_path','file_name','file_size','file');
			
			if(is_array($array) && !empty($array))
				$_filter	=	array_merge($_filter,$array);
			
			return $_filter;
		}
?>