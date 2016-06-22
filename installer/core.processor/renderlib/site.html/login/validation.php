<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>		
<script>
	function PregIt(FormName,FindInput,PregType)
		{
			$("#signupForm").keyup(function() {
				var	UserField	=	$(this).find("#"+FindInput);
				
				switch (PregType) {
						case ('sentence'):
							var ValidInput	=	UserField.val().replace(/[^a-zA-Z\s]/,'');
							break;
						case ('alpha'):
							var ValidInput	=	UserField.val().replace(/[^a-zA-Z]/,'');
							break;
						case ('alphanumeric'):
							var ValidInput	=	UserField.val().replace(/[^0-9a-zA-Z]/,'');
							break;
						default:
							var ValidInput	=	UserField.val().replace(/[^0-9a-zA-Z\_\-]/,'');
					}
				
				UserField.val(ValidInput);
			});
		}
		
	$(document).ready(function() {
		// validate signup form on keyup and submit
		$("#loginForm").validate({
			rules: {
				username: {
					required: true//,
					//minlength: 4
				},
				password: {
					required: true//,
					//minlength: 8
				}
			},
			messages: {
				username: {
					required: "Username Required",
					minlength: "5 Character Minimum"
				},
				password: {
					required: "Password Required",
					minlength: "8 Character Minimum"
				}
			}
		});
		
		// This function will preg replace bad characters
		$("#signupForm").keyup(function() {
				PregIt($(this),'username');
				PregIt($(this),'first_name','sentence');
				PregIt($(this),'last_name','sentence');
			});
		
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				username: {
					required: true,
					minlength: 5
				},
				password: {
					required: true,
					minlength: 8
				},
				confirm_password: {
					required: true,
					minlength: 8,
					equalTo: "#spassword"
				},
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				username: {
					required: "Username Required",
					minlength: "5 Character Minimum"
				},
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
	});
</script>