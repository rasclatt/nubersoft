
<label><div>Allow Sign Up</div>
	<div class="form-input">
		<div>Allow Users to sign up for your site.</div>
		<select name="content[sign_up][toggle]">
			<option value="off">OFF</option>
			<option value="on"<?php echo (isset($settings['sign_up']['toggle']) && $settings['sign_up']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
		</select>
	</div>
</label>