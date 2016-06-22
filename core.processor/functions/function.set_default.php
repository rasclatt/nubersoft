<?php
	function set_default($setto = false, $setfrom = false)
		{
			
			return ($setto == false || empty($setto))? $setfrom : $setto;
		}
?>