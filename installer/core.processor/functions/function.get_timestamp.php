<?php
/*Title: get_timestamp()*/
/*Description: This function saves user timestamps but also returns itself as an object for further use.*/
	function get_timestamp($timespan = 180)
		{
			register_use(__FUNCTION__);
			return new TimeStamp($timespan);
		}
?>