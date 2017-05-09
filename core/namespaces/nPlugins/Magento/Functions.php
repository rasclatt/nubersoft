<?php
namespace nPlugins\Magento;

class Functions extends \Nubersoft\nApp
	{
		/*
		**	@description	Magento 1.x way to validate user password
		*/
		public	function validatePassword($password, $hash)
			{
				$hashArr = explode(':', $hash);
			
				switch (count($hashArr)) {
					case 1:
						return (md5($password) === $hash);
					case 2:
						return (md5($hashArr[1] . $password) === $hashArr[0]);
				}
			}
	}