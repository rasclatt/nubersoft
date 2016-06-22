<?php
/*Title: fetch_token()*/
/*Description: This function fetches a unique number, sets the number to session. Use this function to create form tokens for checking against actions.*/
/*Example: 
`<input type="hidden" name="token[mything]" value="<?php echo fetch_token('mything'); ?>" />`
*/
	function fetch_token($sessionkey = false, $salt = false)
		{
			register_use(__FUNCTION__);
			$salt	=	($salt != false)? $salt:rand(100,999);
			// Save a quick token
			$token	=	md5(uniqid($salt));
			// Save to session if requested
			if($sessionkey != false) {
					$_SESSION['token'][$sessionkey]	=	$token;
				}
			// Return token
			return $token;
		}