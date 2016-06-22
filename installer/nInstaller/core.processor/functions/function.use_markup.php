<?php
/*Title: use_markup()*/
/*Description: This function is the main function that matches all text markup.*/
/*Example: `~app::TestIsBest[setting="value"]~` */
	function use_markup($string = false)
		{
			register_use(__FUNCTION__);
			
			
			if(!empty($string)) {
					if(!is_array($string)) {
							AutoloadFunction('apply_markup');
							
							$val	=	preg_replace_callback('/(\~[^\~]{1,}\~)/i','apply_markup',$string);
							
							return $val;
						}
					else {
							AutoloadFunction('is_admin,printpre');
							if(is_admin()) {
									return _('Input can not be a dataset (array).').printpre($string,__LINE__);
								}
						}
				}
		}
?>