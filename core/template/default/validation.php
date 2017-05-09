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
					minlength: 1
				},
				password: {
					required: true,
					minlength: 5
				},
				confirm_password: {
					required: true,
					minlength: 5,
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
					minlength: "1 Character Minimum"
				},
				password: {
					required: "Password Required",
					minlength: "5 Character Minimum"
				},
				confirm_password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"
				}
			}
		});
	});
</script>