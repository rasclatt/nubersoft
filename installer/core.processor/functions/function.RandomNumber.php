<?php
	function RandomNumber($start = 10000, $end = 99999, $enc = false)
		{
			register_use(__FUNCTION__);
			$start			=	(is_numeric($start))? $start : 10000;
			$end			=	(is_numeric($end) && $end > $start)? $end : ($start*9.99);
			// Randomized number
			$random			=	rand($start,$end) ;
			// Encrypt return or not
			return ($enc == true)? md5(uniqid($random)):uniqid($random);
		}
?>