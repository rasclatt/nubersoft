<?php
function nbr_setTempPassword($email)
	{
		$pDef	=	PasswordGenerator::USE_DEFAULT;
		$pass	=	\Nubersoft\nApp::call()->getFunction('create_random_password');
		$hash	=	PasswordGenerator::Engine($pDef)	->encrypt_password($pass)
														->get_hash();
		$stamp	=	date("Y-m-d H:i:s",strtotime("now"));
		\Nubersoft\nApp::call()->getFunction('nquery')
			->update("users")
			->set(array('reset_password'=>$hash,"timestamp"=>$stamp))
			->where(array("email"=>$email))
			->write();
		
		return array(
			'password'=>$pass,
			'timestamp'=>$stamp
			);
	}