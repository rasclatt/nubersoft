<?php
/*Title: default_jQuery()*/
/*Description: This function will insert the default <head> html for the `jQuery` libraries.*/

	function default_jQuery($force = false)
		{
			AutoloadFunction('check_ssl');
			$ssl	=	($force)? "s" : check_ssl();
			ob_start();
?>
<script type="text/javascript" src="http<?php echo $ssl; ?>://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http<?php echo $ssl; ?>://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script type="text/javascript" src="http<?php echo $ssl; ?>://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<?php		$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}