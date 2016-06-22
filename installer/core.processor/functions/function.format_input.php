<?php
/*Title: format_input()*/
/*Description: This function wraps the `Submits()` class. This class loops through the `$_GET`, `$_POST`, and `$_REQUEST` arrays and applies `htmlentities($value,ENT_QUOTES)` on the values. This function is run on the `config.php` file.*/
/*Example: 
`<input type="hidden" name="token[mything]" value="<?php echo fetch_token('mything'); ?>" />`
*/
	
	function format_input()
		{
			register_use(__FUNCTION__);
			$sanitize	=	new Submits();
			// Loop through and htmlentities sanitize post,get,request
			$sanitize->sanitize();
		}
?>