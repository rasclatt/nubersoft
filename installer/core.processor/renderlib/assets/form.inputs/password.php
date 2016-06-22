<?php if(!isset($values)) return; ?>
	<div class="form-input">
		<?php if(isset($label) && $label == true) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<input type="password" name="<?php echo $name; ?>" value="" placeholder="<?php echo ($values != false)? "Update Password":"Password"; ?>"<?php if(isset($size) && !empty($size)) echo (is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'; ?> />
		<?php if(isset($label) && $label == true) { ?></label><?php } ?>
	</div>