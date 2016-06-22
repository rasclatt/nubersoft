<?php
	function ValidateToken($sessionkey = false,$value = false)
		{
			
			if($sessionkey != false) {
					if(isset($_SESSION['token'][$sessionkey]) && $_SESSION['token'][$sessionkey] == $value)
					return true;
				}
				
			return false;
		}
?>