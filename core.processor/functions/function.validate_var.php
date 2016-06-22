<?php
	function validate_var($array = false,$key = false)
		{
			
			if($array != false) {
					if(is_array($array)) {
							if(isset($array[$key]))
								return true;
						}
				}
			
			return false;
		}
?>