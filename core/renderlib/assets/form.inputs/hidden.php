<?php
use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;

if(!isset($values))
	return;
	
$input['title']			=	(isset($values[$column]) && $values !== 'head')? $values[$column]:"";
$input['label']			=	(!empty($label))? $label : false;
$input['value']			=	(!empty($values[$column]))? $values[$column]:"";
$input['size']			=	(!empty($size))? ((is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'):"";
$input['name']			=	($name)? $name:"";
$input['placeholder']	=	ucwords(str_replace("_"," ",strtolower($input['name'])));
 ?>
	<div class="form-input">
		<div class="nbr_input_hidden"><?php echo $input['title']; ?></div>
		<?php if($input['label']) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<input type="hidden" name="<?php echo $input['name']; ?>" value="<?php echo $input['value']; ?>" placeholder="<?php echo $input['placeholder'] ?>"<?php echo $input['size']; ?> />
		<?php if($input['label']) { ?></label><?php } ?>
	</div>