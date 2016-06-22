<?php
	if(!isset($values))
		return;
		
	$settings['label']	=	(!empty($settings['label']))? $settings['label']:false;
	
	if(is_array($values) && isset($values[$column]))
		$useVals	=	$values[$column];
	elseif(!is_array($values))
		$useVals	=	$values;
	else
		$useVals	=	"";
		
	$input['class']		=	(!empty($settings['class']))? (is_array($settings['class'])? ' class="'.implode(" ",$settings['class']).'"':' class="'.$settings['class'].'"'):false;
?>
	<div class="form-input">
		<?php if($settings['label']) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<textarea name="<?php echo $name; ?>" placeholder="<?php echo ucwords(str_replace("_"," ",strtolower($name))); ?>" <?php if(!empty($size)) echo Safe::decode(Safe::decode($size)); echo $input['class']; ?>><?php echo (isset($useVals))? $useVals:""; ?></textarea>
		<?php if($settings['label']) { ?></label><?php } ?>
	</div>