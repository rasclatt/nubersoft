<?php
function nbr_validTimeStamp($email,$reset = false)
	{
		if($reset)
			return true;
		
		$temp	=	nbr_getTempPassword($email);
		
		if(!empty($temp['reset_password'])) {
			$pass	=	$temp['reset_password'];
			$stamp	=	$temp['timestamp'];
			$expire	=	strtotime($stamp."+ 1 hour");
			$now	=	strtotime("now");
			return ($now > $expire);
		}
		
		return true;
	}