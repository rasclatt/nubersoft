<?php
	function set_default($setto = false, $setfrom = false)
		{
			register_use(__FUNCTION__);
			return ($setto == false || empty($setto))? $setfrom : $setto;
		}
?>