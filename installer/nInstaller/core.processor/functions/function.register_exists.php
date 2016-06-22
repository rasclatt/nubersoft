<?php
/*
** @description	-	This just checks to see if there is a default register file saved
**					in the /client_assets/settings/ folder
*/
function register_exists()
	{
		return (is_file(CLIENT_DIR.'/settings/register.xml'));
	}