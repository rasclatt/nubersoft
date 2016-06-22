<?php
/*Title: get_edit_status()*/
/*Description: This function checks if the toggle for track editing is turned on.*/

	function get_edit_status($unset = false)
		{
			if(isset($_SESSION['toggle']['edit'])) {
				if($unset) {
					unset($_SESSION['toggle']['edit']);
					return false;
				}
				return true;
			}
			return false;
		}