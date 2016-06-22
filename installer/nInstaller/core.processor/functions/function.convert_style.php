<?php
/*Title: convert_style()*/
/*Description: This function just filters and converts `database` columns to `css` values. This function is used in the `get_css_fields()` function which is used inside the `TrackEditor()` class. The `TrackEditor()` class is responsible for converting the page to edit mode.*/

	function convert_style($array = false)
		{	
			register_use(__FUNCTION__);
			if(is_array($array) && !empty($array)) {
					AutoloadFunction('stored_css_filter');
					$_filter = stored_css_filter();
					
					foreach($array as $kind) {
							if(!in_array($kind,$_filter)) {
									$style['converted'][]	=	str_replace("_","-",trim($kind['component_value'],"_"));
									$style['raw'][]			=	$kind['component_value'];
								}
						}
						
					return (isset($style))? $style:false;
				}
		}
?>