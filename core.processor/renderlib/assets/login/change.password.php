<?php
if(!function_exists("AutoloadFunction"))
	return;

AutoloadFunction('site_valid');

if(!nApp::siteValid())
	return;	
	
$userInfo	=	nQuery()	->select("ID")
							->from("users")
							->where(array("email"=>NubeData::$settings->reset_user))
							->fetch();
?>
<style>
div.resetPassWrap	{
	position:absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	clear: both;
	float: left;
	background-color: rgba(0,0,0,0.8);
	z-index: 100000;
	text-align: center;
}
div.resetPassCont	{
	display: inline-block !important;
	max-width: 600px !important;
	margin: 60px auto !important;
	padding: 30px !important;
	box-shadow: 0 0 10px #000 !important;
	background-color: #FFF !important;
}
</style>
<div class="resetPassWrap">
	<div class="resetPassCont nbr_login_window">
		<p>Create a new password.</p>
		<div class="nbr_general_form">
			<form action="" method="post" id="signupForm">
				<input type="hidden" name="action" value="finalize-reset" />
				<input type="hidden" name="reset_password" value="" />
				<input type="hidden" name="timestamp" value="" />
				<input type="hidden" name="ID" value="<?php echo $userInfo[0]['ID']; ?>" />
				<input type="password" name="password" placeholder="NEW PASSWORD" id="password" />
				<input type="password" name="confirm_password" placeholder="CONFIRM PASSWORD" id="confirm" />
				<div class="nbr_button">
					<input disabled="disabled" type="submit" name="update" value="SAVE" />
				</div>
			</form>
		</div>
	</div>
</div>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>	
<script>
					
	// validate signup form on keyup and submit
	$("#signupForm").validate({
		rules: {
			password: {
				required: true,
				minlength: 8
			},
			confirm_password: {
				required: true,
				minlength: 8,
				equalTo: "#password"
			}
		},
		messages: {
			password: {
				required: "Password Required",
				minlength: "8 Character Minimum"
			},
			confirm_password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 8 characters long",
				equalTo: "Please enter the same password as above"
			}
		}
	});
	
	$.validator.addMethod('password', function (value) { 
		return /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/.test(value); 
	}, "Must be: <br />1) At least 8 characters<br /> 2) include a symbol: #?!@$%^&*-<br />3) At least one number<br />4) At least one uppercase<br />5) At least one lowercase");
</script>