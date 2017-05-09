<?php if(!isset($values)) return; ?>
	<div class="form-input">
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo (isset($values[$column]) && $values != false)? $values[$column]:""; ?>" placeholder="<?php echo ucwords(str_replace("_"," ",strtolower($name))); ?>"<?php if(isset($size) && !empty($size)) echo (is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'; ?> />
	</div>