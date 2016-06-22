<?php
if(!function_exists('AutoloadFunction'))
	return;
	
	AutoloadFunction('fetch_token,FetchUniqueId'); ?>
	<div class="emailer-wrapper">
		<?php
			if(isset($sent) && isset($try)) { ?>
		<div class="emailer-error-wrapper">
			<h2 class="error">
				<?php echo ($sent == true)? "{success_head}":"{error_head}"; ?>
			</h2>
			<p class="error">
				<?php echo ($sent == true)? "{success_body}":"{error_body}"; ?>
			</p>
		</div>
		<?php	}
		// Create unique identifier
		$rand	=	substr(FetchUniqueId(),0,6); ?>
		<form method="post" action="#" enctype="multipart/form-data">
			<input type="hidden" name="token[email]" value="<?php echo fetch_token('email',rand(1000,9999)); ?>" />
			<input type="hidden" name="command" value="emailer" />
			<label for="email<?php echo $rand; ?>">Your email address</label>
			<div class="email_form_fields"><input name="email" id="email<?php echo $rand; ?>" type="text" value="" style="width: 96%;"><br /></div>
			<label for="question<?php echo $rand; ?>">Question/Comment</label>
			<div class="email_form_ta"><textarea name="question" id="question<?php echo $rand; ?>" type="text" cols="30" rows="5" style="height: inherit; width: 96%;"></textarea></div>
			<input type="hidden" name="component_id" value="<?php echo $settings['info']['unique_id']; ?>" />
			<input type="hidden" name="email_id" value="<?php echo (isset($settings['info']['email_id']))? $settings['info']['email_id']:'default'; ?>" />
			<div class="formButton"><input disabled="disabled" type="submit" value="SEND"></div>
		</form>
	</div>