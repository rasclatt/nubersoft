<?php
/*Title: is_max_admin()*/
/*Description: This function is responsible for checking if a user is logged in and is the top administrator level.*/
/*Example: 

`AutoloadFunction('is_max_admin');
 if(is_max_admin()) { //Do Admin stuff } else { //Do not admin stuff }`
*/
function is_max_admin()
	{
		$admin	=	(defined("NBR_SUPERUSER"))? (int) NBR_SUPERUSER : (int) 1;
		return is_admin($admin);
	}