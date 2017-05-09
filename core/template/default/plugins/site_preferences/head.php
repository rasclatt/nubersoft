<?php
$token		=	$this->fetchData('token');
$settings	=	$this->toArray($this->getPreferences('head')->content);
?>
<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Header Preferences</p>
<form id="settings_head" method="post" action="<?php echo $this->getDataNode('_SERVER')->HTTP_REFERER ?>">
	<input type="hidden" name="token[nProcessor]" value="<?php echo $token; ?>" />
	<input type="hidden" name="action" value="nbr_edit_system_settings" />
	<input type="hidden" name="page_element" value="settings_head" />
	<label>	Style
		<div class="form-input">
			<textarea name="content[style]" placeholder="Styles will appear inside the <style> tag" class="textarea"><?php echo (isset($settings['style']))? $settings['style']:""; ?></textarea>
		</div>
	</label>
	<label>	Css
		<div class="form-input">
			<textarea name="content[css]" placeholder="Create style sheet links" class="textarea" ><?php echo (isset($settings['css']))? $settings['css']:""; ?></textarea>
		</div>
	</label>
	<label>	Javascript
		<div class="form-input">
			<textarea name="content[javascript]" placeholder="Create JavaScript" class="textarea" ><?php echo (isset($settings['javascript']))? $settings['javascript']:""; ?></textarea>
		</div>
	</label>
	<label>	Javascript Library Links
		<div class="form-input">
			<textarea name="content[javascript_lib]" placeholder="Create JavaScript Links" class="textarea" ><?php echo (isset($settings['javascript_lib']))? $settings['javascript_lib']:""; ?></textarea>
		</div>
	</label>
	<label>	Favicons
		<div class="form-input">
			<textarea name="content[favicons]" placeholder="Link to favicon" id="faviconPath"><?php echo (isset($settings['favicons']))? $settings['favicons']:""; ?></textarea>
		</div>
	</label>
	
	<label>	Default Meta
		<div class="form-input">
			<textarea name="content[meta]" placeholder="Create default meta" class="textarea" ><?php echo (isset($settings['meta']))? $settings['meta']:""; ?></textarea>
		</div>
	<label>	Html
		<div class="form-input">
			<textarea name="content[html][value]" placeholder="Create HTML to replace tempate mast head" class="textarea" ><?php echo (isset($settings['html']['value']))? $settings['html']['value']:""; ?></textarea>
		</div>
		<div class="form-input">
			<select name="content[html][toggle]">
				<option value="off">OFF</option>
				<option value="on"<?php echo (isset($settings['html']['toggle']) && $settings['html']['toggle'] == 'on')? ' selected="selected"':""; ?>>ON</option>
			</select>
		</div>
	</label>
	<div class="nbr_button">
		<input type="submit" name="update" value="SAVE" />
	</div>
</form>