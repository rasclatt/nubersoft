<?php
/*Title: FetchLink()*/
/*Description: This function will create a list of `unique_id` keys in an array for use in building a directory structure. Used in the `PageBuilder()` class. Outside of this class it has little value.*/

	function FetchLink($unique_id = false, $saveinfo = true)
		{
			register_use(__FUNCTION__);
			// Build recursive directory look up
			$allMenus	=	new recurseInclude();
			$response	=	$allMenus->fetch("select * from `main_menus` order by link ASC", 'unique_id', false, true)->response;
			if(!is_array($response)) {
					$return_arr['dir']		=	false;
					
					if($saveinfo == true)
						$return_arr['info']	=	false;
				}
			else {
					foreach($response['page'] as $keys => $values) {
							$new_array				=	$allMenus->BuildSiteFiles($response['info'],'unique_id',$keys,$values);
							
							foreach($new_array as $object) {
									
									// The foreach returns either value depending on sub levels
				
									$_unique_id		=	$object['ret_val'];
									$ids[]			=	$_unique_id;
									$_directory[]	=	$object['rec_dir'];
								}
						}
						
					// Save directories
					$return_arr['dir']		=	$_directory;
					// Save unique_ids
					$return_arr['ids']		=	$ids;
					// Return only the directory if in list of menus
					if($unique_id != false) {
							$dir	=	array_search($unique_id,$return_arr['ids']);
							if($dir !== false) {
									return $return_arr['dir'][$dir];
								}
						}
					
					if($saveinfo == true)
						$return_arr['info']		=	$response['info'];
				}
				
			return	$return_arr;
		}
?>