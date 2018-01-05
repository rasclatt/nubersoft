<?php echo $this->renderSiteLogo(array('style'=>'max-width: 200px;')) ?>
<h1 style="margin: 0 0 5px 0;">Forgot Password?</h1>
<p>Send yourself an email to retrieve your username.</p>
<div class="nbr_form_general">
<form action="<?php echo $this->siteUrl(); ?>" class="nbr_ajax_form" data-instructions='{"action":"recover_password_request"}'>
	<input type="hidden" name="action" value="recover_password_request" />
	<input type="hidden" name="token[nProcessor]" value="" id="forgot_token" />
	<div class="login_fields">
		<input type="text" id="forgot_email" name="email" placeholder="Your email address" />
	</div>
	<div class="nbr_contain">
			<a href="#" class="nbr_button cancel nTrigger" data-instructions='<?php echo json_encode(array('FX'=>['acton'=>['#loadspot_modal'],'fx'=>['removeClass'],'subfx'=>['visible']],'html'=>array(' '),'sendto'=>array('#loadspot_modal'),'events'=>array('click'))) ?>'>CANCEL</a>
		<div class="nbr_button">
			<input type="submit" value="SEND" name="submit" class="disabled-submit" disabled id="forgot_submit" />
		</div>
	</div>
</form>
</div>
<script>
fetchAllTokens($);
$('#forgot_email').on('keyup',function() {
	var	disabledButton	=	$('#forgot_submit');
	var is_disabled		=	true;
	is_disabled	=	($(this).val() == '' && $('#forgot_token').val() != '');
	disabledButton.attr('disabled',is_disabled);
});
</script>