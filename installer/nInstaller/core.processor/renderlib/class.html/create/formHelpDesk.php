<?php if(!isset($this->inputArray)) return; ?>

	<div class="component_buttons_wrap">
		<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" method="post">
			<input type="hidden" name="helpdesk" value="<?php echo (isset($_SESSION['helpdesk']))? 'off': 'on'; ?>" /><?php 
			$chechToggle	=	(isset($_SESSION['helpdesk']))? 'on': 'off'; ?>
			<div class="help_button_<?php echo $chechToggle; ?>"><input disabled="disabled" type="submit" name="submit" value="HELP" /></div>
		</form>
	</div>