<?php
/*Title: decode_serial()*/
/*Description: This will check if there is a specific key in an `array` slated to be unserialized. This is primarily used to `unserialize` `component` data. Outside of this scope, it has little value.*/

	function decode_serial(&$array = false,$key = false,$where = 'submenu')
		{
			register_use(__FUNCTION__);
			AutoloadFunction('check_empty,organize,get_form_layout');
			// Check if array keys exist
			if(!empty($array[$key])) {
					// Unserialize
					$options	=	unserialize(Safe::decode($array[$key]));
					// Remove original serialized column
					unset($array[$key]);
					// Merge the arrays
					$array	=	array_merge($array,$options);
				}
				
			// Return the comparison array
			if($where == 'submenu')
				$wharry	=	array("component_name"=>"sub_menus");
			else
				$wharry	=	array("component_name"=>"components");
			
			$prefs['where']		=	$wharry;
			$prefs['select']	=	"*";
			$formlayout			=	get_form_layout($prefs);
			
			if(!empty($formlayout)) {
					foreach($formlayout as $rows) {
							if(preg_match("/".$key."/",$rows['component_value']))
								$serialkeys[]	=	preg_replace('/(['.$key.'\[]{10})([^\]]{1,})([\]]{1})/',"$2",$rows['component_value']);
						}
						
					return (isset($serialkeys))? $serialkeys:false;
				}
		}
?>