<?php
/*Title: create_track_component()*/
/*Description: This function is used on the `/core/renderlib/component.track/component.window.php` file to create components for the `Track Editor`. It is a wrapper for the `ComponentEditor()` class which actually does the work for creating a component. Outside of this scope it has little value.*/

	function create_track_component($settings = array())
		{
			
			$comp_id	=	(isset($settings['unique_id']) && !empty($settings['unique_id']))? $settings['unique_id']: false;
			$page_id	=	(isset($settings['ref_page']) && !empty($settings['ref_page']))? $settings['ref_page']: false;
			$component	=	new ComponentEditor($nuber,$comp_id,$page_id);
			
			// Secure bind statement
			if($page_id != false) {
					AutoloadFunction('nQuery');
					$nubquery	=	nQuery();
					$data		=	$nubquery->select()->from("components")->where(array("unique_id"=>$comp_id))->fetch();
				
					if(isset($data[0]))
						$data	=	$data[0];
				}
			else
				$data	=	array();

			$component->Display($data);
		}
?>