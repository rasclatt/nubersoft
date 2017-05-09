<?php
$form	=	\Nubersoft\nApp::call('nForm');
$inputs['nProcessor']	=	
				array(
					"type"=>"hidden",
					"name"=>"token[nProcessor]",
					"value"=>\Nubersoft\nApp::call('nToken')->setMultiToken('nProcessor','email_user_password')
				);
$inputs['init']	=	
				array(
					"type"=>"hidden",
					"name"=>"action",
					"value"=>"email_user_password"
				);
$inputs['email']	=	
				array(
					"type"=>"text",
					"name"=>"data[email]",
					"label"=>"Type in your email address."
				);
$inputs['update']	=
				array(
					"type"=>"submit",
					"name"=>"update",
					"value"=>"SEND",
					"class"=>"disabled-submit",
					"disabled"=>true
				);

?>
<div style="text-align: center;">
	<div class="nbr_login_window" style="text-align: center; margin: 0 auto;">
		<div style="max-width: 300px; margin: 0 auto;" id="forgot-pass-load">
			<h2>Send Password Reset Request</h2>
			<p>Fill out the information below and a temporary password will be sent to your email on file</p>
			<div class="nbr_general_form">
			<form id="forgotten-password" action="<?php echo site_url(); ?>" method="POST">
				<?php echo $form->fullhide($inputs['nProcessor']); ?>
				<?php echo $form->fullhide($inputs['init']); ?>
				<?php echo $form->text($inputs['email']); ?>
				<div class="inputWrap">
					<div class="nbr_button">
						<?php echo $form->submit($inputs['update']); ?>
					</div>
				</div>
				<div class="inputWrap">
					<div class="closer" onClick="window.location='<?php echo (\Nubersoft\nApp::call()->isAjaxRequest())? '' : \Nubersoft\nApp::call()->getFunction('site_url').\Nubersoft\nApp::call()->getPage('full_path'); ?>'">Close</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>	
<script>
$('.disabled-submit').removeAttr('disabled');
$(document).ready(function() {
	var	thisForm	=	$("#forgotten-password");
	// validate signup form on keyup and submit
	thisForm.validate({
		errorHandler:function(form,e) {
			e.preventDefault();
		},
		rules: {
			"data[email]": {
				required: true,
				minlength: 5,
				email: true
			}
		},
		messages: {
			username: {
				required: "Username Required",
				minlength: "5 Character Minimum"
			},
			email: {
				required: "Email Required",
				minlength: "5 Character Minimum",
				email:	"Must be valid email address"
			}
		}
	});
});
</script>