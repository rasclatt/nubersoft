<?php
	function InputFields($table = false, $display = false)
		{
			
			
			if($table == false)
				return false;
				
			AutoloadFunction('get_dropdowns,check_empty');
			
			$query		=	get_dropdowns($table);
			
			if($query != 0 && $display == true) {
					foreach($query as $select => $options) {
							$design[]	=	'<select name="'.$select.'">';
							foreach($options as $settings) {
									if(check_empty($settings,'page_live','on'))
										$design[]	=	'<option value="'.$settings['menuVal'].'">'.$settings['menuName'].'</option>';
								}
							$design[]	=	'</select>';
						}
						
					return (isset($design))? $design:false;
				}
			else
				return $query;
		}
?>