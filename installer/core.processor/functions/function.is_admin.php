<?php
/*Title: is_admin()*/
/*Description: This function is responsible for checking if a user is logged in and is an administrator.*/
/*Example: 

`AutoloadFunction('is_admin');
 if(is_admin()) { //Do Admin stuff } else { //Do not admin stuff }`
*/
	function is_admin($admin = false)
		{
			$admin	=	(defined("NBR_ADMIN"))? (int) NBR_ADMIN : (int) 2;
			return (isset($_SESSION['usergroup']) && ($_SESSION['usergroup'] <= $admin));
		}