<?php
/*Title: bind_array()*/
/*Description: This function will recursively iterate through an `array` and create `bind` keys using a colon (`:`). Also included is the `filter_action_words()` which returns an `array` of reserved words to remove (UNDER CONSTRUCTION). */

	function bind_array(array $_payload)
		{
			
			AutoloadFunction('filter_action_words');
			$filter			=	filter_action_words();
			
			$return			=		array();
			foreach($_payload as $key => $value) {
					$key	=	":".$key;
			
					if(is_array($value))
						$value = bind_array($value); 
		
					$return[$key] = $value;
				}
			
			return $return;

		}
?>