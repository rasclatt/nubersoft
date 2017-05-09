<?php
function nbr_getEmailAddr($email,&$id = false)
	{
		$fetch	=	\Nubersoft\nApp::call()->getFunction('nquery')	
						->select(array('email','ID'))
						->from("users")
						->where(array('email'=>$email))
						->getResults();
									
		$getEmail	=	($fetch != 0)? \Nubersoft\nApp::call('Safe')->decode($fetch[0]['email']) : false;
		
		if($getEmail)
			$id	=	$fetch[0]['ID'];
			
		return $getEmail;
	}