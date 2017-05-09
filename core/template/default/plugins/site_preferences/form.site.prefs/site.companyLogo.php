
<label><div>Company Logo</div>
	<div class="form-input">
		<input type="text" id="companylogoPath" name="content[companylogo]" value="<?php echo (!empty($settings['companylogo']))? $settings['companylogo'] : "/client/images/logo/default.png"; ?>" placeholder="Local link to your logo" />
	</div>
</label>
<label><div>Change the live-status of the site. Your site is currently <?php echo ($this->siteLive())? "ON":"OFF"; ?></div>
	<div class="form-input">
		<textarea name="content[site_live][value]" placeholder="Site down for maintenance HTML" class="textarea" ><?php echo (isset($settings['site_live']['value']))? $settings['site_live']['value']:""; ?></textarea>
		<div style="display: table-row;">
			<select name="content[site_live][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo ($this->siteLive())? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</div>
</label>