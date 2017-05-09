<?php
use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;
?>

					<div id="sign_up" style="display: none;">
						<form method="post" action="" enctype="application/x-www-form-urlencoded" id="signupForm">
							<input type="hidden" name="errors" value="on" />
							<label for="first_name">First Name</label>
							<div class="login_fields">
								<?php echo self::call('nForm')->text(array('name'=>'first_name','id'=>'first_name','style'=>array('width: 98%'),'class'=>'no-paste')); ?>
							</div>
							<label for="last_name">Last Name</label>
							<div class="login_fields">
								<input type="text" name="last_name" id="last_name" style="width: 98%;" class="no-paste" />
							</div>
							<label for="email">Email</label>
							<div class="login_fields">
								<input type="text" name="email" id="email" style="width: 98%;" class="no-paste usercheck"  />
							</div>
							<label for="username">Username</label>
							<div class="login_fields">
								<input type="text" name="username" id="username" style="width: 98%;" class="no-paste usercheck" />
							</div>
							<label for="password">Password</label>
							<div class="login_fields">
								<input type="password" name="password" id="spassword" style="width: 98%;" class="no-paste usercheck" />
							</div>
							<label for="confirm_password">Confirm Password</label>
							<div class="login_fields">
								<input type="password" name="confirm_password" id="confirm_password" style="width: 98%;" class="no-paste" />
							</div>
							<input type="hidden" name="action" value="sign_up" />
							<div class="nbr_contain">
								<div class="nbr_button">
									<input disabled="disabled" type="submit" name="add" value="Sign Up" id="sign_up_button" class="disabled-submit" />
								</div>
							</div>
						</form>
						<div class="nbr_contain">
							<div id="use-error-block" class="nbr_invalid_msg">
								<i class="nFont">Invalid username or email. You may have already signed up with these credentials.</i>
							</div>
							<div id="scriptor">
							</div>
							<p class="after_text nButton nFont" data-hide="#sign_up" data-acton="#login">Already a member? <span class="after_link">Log in now!</span></p>
						</div>
					</div>