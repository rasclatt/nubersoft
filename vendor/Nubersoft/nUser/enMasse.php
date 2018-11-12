<?php
namespace Nubersoft\nUser;

use \Nubersoft\nUser\Controller as User;

trait enMasse
{
	public	function isAdmin()
	{
		return (new User())->{__FUNCTION__}();
	}
	
	public	function isLoggedIn()
	{
		return (new User())->{__FUNCTION__}();
	}
}