<?php
use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;

if(!isset($values))
	return;
?>
	<div class="form-input">
		<?php if(isset($label) && $label == true) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<input type="text" name="<?php echo $name; ?>" value="<?php echo (isset($values[$column]) && $values != false)? $values[$column]:""; ?>" placeholder="<?php echo ucwords(str_replace("_"," ",strtolower($name))); ?>"<?php if(isset($size) && !empty($size)) echo (is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'; ?> disabled />
		<?php if(isset($label) && $label == true) { ?></label><?php } ?>
	</div>