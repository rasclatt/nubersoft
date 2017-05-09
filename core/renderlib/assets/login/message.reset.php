<?php
if(!function_exists('AutoloadFunction'))
	return;

$form	=	new FormBuilder();
$form	->addField(array("type"=>"text","options"=>array("name"=>"email","label"=>"Type in your email address.")))
		//->addField(array("type"=>"text","options"=>array("name"=>"username")))
		->addField(array("type"=>"hidden","options"=>array("name"=>"command","value"=>"forgot_pass")))
		->addField(array("type"=>"submit","options"=>array("name"=>"update","value"=>"SEND","class"=>"disabled-submit","disabled"=>true)))
		->compile(FormBuilder::NO_BUTTON);
?>
<div style="text-align: center;">
	<div class="nbr_login_window" style="text-align: center; margin: 0 auto;">
		<div style="max-width: 300px; margin: 0 auto;" id="forgot-pass-load">
			<h2>Send Password Reset Request</h2>
			<p>Fill out the information below and a temporary password will be sent to your email on file</p>
			<div class="nbr_general_form">
			<form id="forgotten-password" method="POST">
				<input type="hidden" name="command" value="forgot_pass" />
				<?php echo $form->getFieldHtml("email"); ?>
				<?php echo $form->getFieldHtml("command"); ?>
				<div class="inputWrap">
					<div class="nbr_button"><?php echo $form->getFieldHtml("update"); ?></div>
				</div>
				<div class="inputWrap">
					<div class="closer" onClick="window.location=''">Close</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>		
<script>
	$('.disabled-submit').removeAttr('disabled');
	$(document).ready(function() {
		var	thisForm	=	$("#forgotten-password");
		// validate signup form on keyup and submit
		thisForm.validate({
			submitHandler: function(form,event) {
					event.preventDefault();
					$.ajax({
							url:'/ajax/send.password.php',
							type: 'post',
							data: $(form).serialize(),
							success: function(result) {
									$("#adminWrap").html(result);
								}
						});
						
				},
			rules: {
				username: {
					required: true,
					minlength: 5
				},
				email: {
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