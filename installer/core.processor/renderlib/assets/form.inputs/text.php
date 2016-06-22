<?php
	if(!isset($values)) return;
	
	$input['class']			=	(!empty($settings['class']))? (is_array($settings['class'])? ' class="'.implode(" ",$settings['class']).'"':' class="'.$settings['class'].'"'):false;
	$input['title']			=	(isset($values[$column]) && $values !== 'head')? $values[$column]:"";
	$input['label']			=	(!empty($settings['label']))? $settings['label'] : false;
	$input['value']			=	(!empty($values[$column]))? $values[$column]:"";
	$input['size']			=	(!empty($size))? ((is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'):"";
	$input['name']			=	($name)? $name:"";
	$input['placeholder']	=	ucwords(str_replace("_"," ",strtolower($input['name'])));
	$input['disabled']		=	(isset($disabled))? $disabled : "";
	
 ?>
	<div class="form-input">
		<?php if($input['label']) { ?><label><span class="label-hd"><?php echo $input['label']; ?></span><?php } ?>
		<input type="text" name="<?php echo $input['name']; ?>" value="<?php echo $input['value']; ?>" placeholder="<?php echo $input['placeholder']; ?>"<?php echo $input['size']; echo $input['disabled']; echo $input['class']; ?> />
		<?php if($input['label']) { ?></label><?php } ?>
	</div>