<?php
/*Title:  get_default_htaccess()*/
/*Description: This function will write to disk the default `.htaccess` or any .htaccess script fed into it as an arguement*/

function get_default_htaccess($settings = false)
	{
		$nReWriter	=	new \Nubersoft\nReWriter();
		return $nReWriter->getDefault($settings);
	}