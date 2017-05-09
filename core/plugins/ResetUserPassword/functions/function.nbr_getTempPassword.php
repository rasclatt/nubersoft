<?php
function nbr_getTempPassword($email)
	{
		$query	=	\nApp::getFunction('nquery')	->select(array('reset_password','timestamp'))
													->from("users")
													->where(array("email"=>$email))
													->getResults();
													
		return ($query != 0)? $query[0] : 0;
	}