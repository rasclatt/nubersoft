<?php 
	if(!function_exists("autoload_function"))
		return;
?>	<div class="form-input">
<?php if(!empty($label)) { ?>
	<label><span class="label-hd"><?php if(isset($settings['label'])) echo $settings['label']; ?></span><?php } 
?>		<select name="<?php echo $name; ?>">
			<option value="">Select</option>
<?php
	if(!empty($dropdowns[$name])) {
		foreach($dropdowns[$name] as $opt) {
			
			if(isset($opt['menuVal']))
				$opt['value']	=	$opt['menuVal'];
			
			if(isset($opt['menuName']))
				$opt['name']	=	$opt['menuName'];
				
			if(isset($opt['value'])) {
?>			<option value="<?php echo $opt['value']; ?>"<?php if(isset($values[$name]) && $values[$name] == $opt['value']) { ?>selected<?php } ?>><?php echo $opt['name']; ?></option>
<?php 		}
		}
	}
?>		</select>
<?php if(!empty($label)) {
?>	</label>
<?php }
?>	</div>