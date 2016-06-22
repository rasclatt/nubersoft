<?php
	$input					=	array();
	$input['class']			=	"";
	
	if(!empty($opts['class'])) {
			$input['class']	=	(!empty($opts['class']) && is_array($opts['class']))? ' class="'.implode(" ",$opts['class']).'"':' class="'.$opts['class'].'"';
		}
		
	$input['id']				=	(!empty($opts['title']))? ' id="'.$opts['id'].'"' : false;
	$input['title']			=	(!empty($opts['title']))? $opts['title'] : false;
	$input['label']			=	(!empty($opts['label']))? $opts['label'] : false;
	$input['value']			=	(!empty($opts['value']))? $opts['value'] : false;
	$input['size']			=	(!empty($opts['size']))? ((is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'):"";
	$input['name']			=	(!empty($opts['name']))? $opts['name'] : "untitled_input";
	$input['placeholder']	=	(!empty($opts['placeholder']))? $opts['placeholder'] : false;
	$input['disabled']		=	(!empty($opts['disabled']))? "disabled" : "";
	$input['wrapper']		=	(!empty($opts['wrapper']))? $opts['wrapper'] : "form-input";
?>
	<div class="form-input">
		<?php if(!empty($input['label'])) { ?><label><span class="label-hd"><?php echo $input['label']; ?></span><?php } ?>
		<input type="password" name="<?php echo $input['name']; ?>" value="" placeholder="<?php echo ($input['value'] != false)? "Update Password":"Password"; ?>"<?php if(isset($input['size']) && !empty($input['size'])) echo (is_numeric($input['size']))? 'size="'.$input['size'].'"':' style="width: '.Safe::decode($input['size']).';"'; ?> />
		<?php if(!empty($input['label'])) { ?></label><?php } ?>
	</div>