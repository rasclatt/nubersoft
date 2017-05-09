<?php
/*
**	@desc	Check if the current usergroup is greater or equal to param (greater value means less permissions)
**	@param	[int]
*/
function allow_if($usergroup = 3)
	{
		return (nApp::getUser("usergroup") <= $usergroup);
	}