<?php 
	if(!function_exists("AutoloadFunction"))
		return;
		
	if(isset($dropdowns) && !empty($dropdowns)) {

			if(isset($dropdowns[$name])) {
					if(isset($dropdowns[$name][0]))
						$options	=	$dropdowns[$name];
					else
						$options[]	=	$dropdowns[$name];
				}
		}
		
	AutoloadFunction('nQuery');
	$nubquery	=	nQuery();
	
	if(!isset($options))
		$options	=	$nubquery	->select(array("menuName","menuVal"))
									->from("dropdown_menus")
									->where(array("assoc_column"=>$name))
									->Record(__FILE__)
									->fetch(); ?>
		
	<div class="form-input">
		<?php if(isset($label) && $label == true) { ?><label><span class="label-hd"><?php if(isset($settings['label'])) echo $settings['label']; ?></span><?php } ?>
		<select name="<?php echo $name; ?>">
			<option value="">Select</option>
			<?php if(!empty($options)) {
				foreach($options as $opt) {
						if(isset($opt['menuVal'])) { ?>
			<option value="<?php echo $opt['menuVal']; ?>"<?php if(isset($values[$name]) && $values[$name] == $opt['menuVal']) { ?>selected<?php } ?>><?php echo $opt['menuName']; ?></option>
				<?php } } ?>
			<?php } ?>
		</select>
		<?php if(isset($label) && $label == true) { ?></label><?php } ?>
	</div>