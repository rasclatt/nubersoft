<?php
$token		=	$this->fetchData('token');
$settings	=	$this->toArray($this->getPreferences('foot')->content);
?>
<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Footer Preferences</p>
<form id="setting_settings_foot" method="post" action="<?php echo $this->getDataNode('_SERVER')->HTTP_REFERER ?>">
	<input type="hidden" name="token[nProcessor]" value="<?php echo $token ?>" />
	<input type="hidden" name="action" value="nbr_edit_system_settings" />
	<input type="hidden" name="page_element" value="settings_foot" />
	<label>YouTube 
		<div class="form-input">
			<input type="text" name="content[youtube][value]" placeholder="YouTube Link" value="<?php echo (isset($settings['youtube']['value']))? $settings['youtube']['value'] : ""; ?>" />
			<select name="content[youtube][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['youtube']['toggle']) && $settings['youtube']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<label>Facebook
		<div class="form-input">
			<input type="text" name="content[facebook][value]" placeholder="Facebook Link" value="<?php echo (isset($settings['facebook']['value']))? $settings['facebook']['value'] : ""; ?>" />
			<select name="content[facebook][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['facebook']['toggle']) && $settings['facebook']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<label>Twitter
		<div class="form-input">
			<input type="text" name="content[twitter][value]" placeholder="Twitter Link" value="<?php echo (isset($settings['twitter']['value']))? $settings['twitter']['value'] : ""; ?>" />
			<select name="content[twitter][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['twitter']['toggle']) && $settings['twitter']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<label>Pinterest
		<div class="form-input">
			<input type="text" name="content[pinterest][value]" placeholder="Pinterest Link" value="<?php echo (isset($settings['pinterest']['value']))? $settings['pinterest']['value'] : ""; ?>" />
			<select name="content[pinterest][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['pinterest']['toggle']) && $settings['pinterest']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<label>Html
		<div class="form-input">
		<?php //echo printpre($footer); ?>
			<textarea name="content[html][value]" placeholder="Custom HTML" class="textarea" ><?php echo (!empty($settings['html']['value']))? $settings['html']['value'] : ""; ?></textarea>
		</div>
		<div class="form-input">
			<select name="content[html][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['html']['toggle']) && $settings['html']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<div class="nbr_div_button nbr_formadd" data-formadd="setting_settings_foot" data-formgroup="footer" style="font-size: 14px; padding: 8px 16px; float: right;">ADD NEW SOCIAL MEDIA</div>
		<?php
		if(!empty($settings['social_media'])) {
			$settings['social_media']	=	$this->toArray($settings['social_media']);
		?>
		<div style="display: inline-block; width: 100%;">
			<div style="padding: 10px; background-color: #555; margin: 20px 0;">
				<h2 style="color: #CCC;">Additional Social Media</h2>
			<?php
			
			foreach($settings['social_media'] as $key => $vals) {
			?>
				<div class="form_custElemWrap">
					<div class="form_removethis" style="color: #CCC; background-color: #000; padding: 15px 8px; border-radius: 3px; font-size: 13px; display: inline-block; float: right;">DELETE</div>
					<span>
						<label style="margin: 0 0 10px 0; padding: 0; width: auto; border: none; float: left; display: inline-block;"><?php echo $custName = ucwords(str_replace("_"," ", $key)); ?></label>
							<div class="form-input">
								<input type="text" name="content[social_media][<?php echo $key; ?>][url]" value="<?php echo (!empty($vals['url']))? $vals['url'] : ""; ?>" placeholder="User URL for <?php echo $custName; ?>" size="30" style="width: auto;" />
								<input type="text" name="content[social_media][<?php echo $key; ?>][img]" value="<?php echo (!empty($vals['img']))? $vals['img'] : ""; ?>" placeholder="Image icon for <?php echo $custName; ?>" size="30" style="width: auto;" />
								<?php if(!empty($vals['img'])) { ?>
								<img src="<?php echo $vals['img']; ?>" style="max-height: 40px;" />
								<?php } ?>
							</div>
						
					</span>
					<hr style="border: 1px dashed #CCC; width: 99%;" />
				</div>
			<?php
			}
			?>
			</div>
		</div>
		<?php
		}
		?>
	<div class="nbr_button">
		<input type="submit" name="update" value="SAVE" />
	</div>
</form>