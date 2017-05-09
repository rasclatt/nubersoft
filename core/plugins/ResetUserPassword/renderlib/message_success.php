<?php
if(!isset($returned))
	exit;
	
autoload_function("fetch_token");
?>
<style>
.resetInstr	{
	padding: 10px 15px;
	background-color: #0C3;
	border: 5px solid #FFF;
	box-shadow: 1px 1px 5px rgba(0,0,0,0.4);
	text-shadow: 1px 1px 3px #000;
	color: #FFF;
	display: none;
}
div.resetWindow label {
	font-size: 12px;
	margin-bottom: 8px;
}
div.resetWindow input	{
	width: 94%;
}
div.nbr_login_window	{
	max-width: 400px;
}
input:disabled	{
	opacity: 0.5;
}
</style>
<div style="padding: 10px; min-height: 100px; display: inline-block;">
	<div style="display:block; clear: left; float: left;">
		<h3><?php echo (isset($returned))? $returned['h']:"Whoops!"; ?></h3>
		<p><?php echo (isset($returned))? $returned['p']:"Unknown error"; ?></p>
		<div class="closer" onClick="window.location=''">Close</div>
	</div>
	<div style="margin-top: 20px; display:block; clear: left; float: left;">
		<div class="nbr_login_window resetWindow">
			<p class="resetInstr fader">Please check your email for your reset password.</p>
			<form action="" id="login-temp" method="post">
				<input type="hidden" name="token[change_pass]" value="<?php echo fetch_token('change_pass'); ?>" />
				<input type="hidden" name="action" value="login-temp" />
				<label>When you receive your password key, paste in this field.</label>
				<input type="password" name="password" placeholder="password" style="margin-bottom: 10px;" />
				<label>Retype the email address you used to reset your password.</label>
				<input type="text" name="email" placeholder="email" style="margin-bottom: 10px;" />
				<div class="inputWrap">
					<div class="nbr_button"><input disabled="disabled" type="submit" name="update" value="CHANGE" /></div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$(".fader").delay(600).fadeIn("slow");
	
	var	password;
	var	email;
	
	$("#login-temp").keyup(function() {
			password	=	$("input[name='password']").val();
			email		=	$("input[name='email']").val();
			
			if(password != '' && email != '')
				$("input[name='update']").prop("disabled",false);
			else
				$("input[name='update']").prop("disabled",true);
		});
});
</script>