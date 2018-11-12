<?php
namespace Nubersoft\nUser;

class Controller extends \Nubersoft\nUser
{
	private	static	$Session;
	
	public	function isLoggedIn()
	{
		return (!empty($this->getDataNode('_SESSION')['username']));
	}
	
	public	function isAdmin()
	{
		$SESS	=	$this->getDataNode('_SESSION');
		if(empty($SESS['usergroup']))
			return false;
		
		if(!is_numeric($SESS['usergroup']))
			$SESS['usergroup']	=	constant($SESS['usergroup']);
		
		return ($SESS['usergroup'] <= NBR_ADMIN);
	}
}