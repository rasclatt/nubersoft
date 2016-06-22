<?php if(!isset($this->inputArray)) return; ?>

		<div class="component_buttons_wrap">
			<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" method="post">
				<input type="hidden" name="requestTable" value="<?php echo fetch_table_id('system_settings'); ?>" />
				<input type="hidden" name="name" value="tinyMCE" />
				<input type="hidden" name="tinyMCE" value="<?php echo (isset($_SESSION['tinyMCE']))? 'off': 'tymce'; ?>" /><?php 
				$chechToggle	=	(isset($_SESSION['tinyMCE']))? 'on': 'off'; ?>
				<div class="tinyMCE_button_<?php echo $chechToggle; ?>"><input disabled="disabled" type="submit" name="submit" value="WYSIWYG" /></div>
			</form>
		</div>