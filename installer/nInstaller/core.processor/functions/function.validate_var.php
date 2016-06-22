<?php
	function validate_var($array = false,$key = false)
		{
			register_use(__FUNCTION__);
			if($array != false) {
					if(is_array($array)) {
							if(isset($array[$key]))
								return true;
						}
				}
			
			return false;
		}
?>