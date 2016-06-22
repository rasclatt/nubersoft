<?php
if(!function_exists('AutoloadFunction'))
	return;
	
	AutoloadFunction('fetch_token'); ?>
	<form method="post" action="#" enctype="multipart/form-data">
		<input type="hidden" name="token[email]" value="<?php echo fetch_token('email',rand(1000,9999)); ?>" />
		<label for="email">Your email address</label>
		<div class="email_form_fields"><input name="email" id="email" type="text" value="" style="width: 96%;"><br /></div>
		<label for="question">Question/Comment</label>
		<div class="email_form_ta"><textarea name="question" id="question" type="text" cols="30" rows="5" style="height: inherit; width: 96%;"></textarea></div>
		<input type="hidden" name="component_id" value="<?php echo $this->_info['unique_id']; ?>" />
		<input type="hidden" name="email_id" value="<?php echo (isset($this->email_id))? $this->email_id:'default'; ?>" />
		<div class="formButton"><input disabled="disabled" type="submit" value="SEND"></div>
	</form>