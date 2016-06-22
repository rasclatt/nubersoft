<?php
	function ValidateToken($sessionkey = false,$value = false)
		{
			register_use(__FUNCTION__);
			if($sessionkey != false) {
					if(isset($_SESSION['token'][$sessionkey]) && $_SESSION['token'][$sessionkey] == $value)
					return true;
				}
				
			return false;
		}
?>