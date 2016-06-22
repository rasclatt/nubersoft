<?php
/*Title: get_key_value()*/
/*Description: This will search through an `array()` and return the value of a matched or like `key`.*/
	function get_key_value($array = array(), $find = array(),$recursive = true)
		{
			register_use(__FUNCTION__);
			$finder	=	new RecurseSearch();
			return $finder->Find($array,$find,$recursive);
		}
?>