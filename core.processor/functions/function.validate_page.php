<?php
	function validate_page()
		{
			
			$page		=	new ValidateLoginState($nuber);
			$required	=	 $page->Validate($nuber->page_prefs['session_status'])->login_required;
			return $required;
		}
?>