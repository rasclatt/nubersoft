<?php
	class	PasswordGenerator
		{
			private	static	$singleton;
			private	static	$engine_type;
			
			const	BCRYPT		=	'bcrypt';
			const	PASS_HASH	=	'password_hash';
			const	USE_DEFAULT	=	'password_hash';
			
			public	static	function Engine($type = 'password_hash',$rounds = false)
				{
					$reset	=	false;
					
					if(!empty(self::$engine_type)) {
							if(self::$engine_type !== $type)
								$reset	=	true;
						}
					else
						self::$engine_type	=	$type;
					
					if(!$reset) {
							if(!empty(self::$singleton))
								return self::$singleton;
						}
					
					self::$singleton	=	($type == 'password_hash' && function_exists("password_hash"))? new PasswordVerify($rounds) : new Bcrypt($rounds);

					return self::$singleton;
				}
		}