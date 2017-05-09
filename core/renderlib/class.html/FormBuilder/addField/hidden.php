<?php
	$input					=	array();
	$input['class']			=	"";
	
	if(!empty($opts['class'])) {
			$input['class']	=	(!empty($opts['class']) && is_array($opts['class']))? ' class="'.implode(" ",$opts['class']).'"':' class="'.$opts['class'].'"';
		}
	$input['id']			=	(!empty($opts['id']))? ' id="'.$opts['id'].'"' : false;
	$input['title']			=	(!empty($opts['title']))? $opts['title'] : false;
	$input['label']			=	(!empty($opts['label']))? $opts['label'] : false;
	$input['value']			=	(!empty($opts['value']))? $opts['value'] : false;
	$input['name']			=	(!empty($opts['name']))? $opts['name'] : "untitled_input";
	$input['disabled']		=	(!empty($opts['disabled']))? "disabled" : "";
	$input['wrapper']		=	(!empty($opts['wrapper']))? $opts['wrapper'] : "form-input";
?>
	<div class="<?php echo $input['wrapper']; ?>">
<?php if($input['label']) {
?>		<label><span class="label-hd"><?php echo $input['label']; ?></span>
<?php 	}
?>		<input type="hidden" name="<?php echo $input['name']; ?>" value="<?php echo $input['value']; ?>"<?php echo $input['id']; ?><?php echo $input['disabled']; echo $input['class']; ?> />
<?php if($input['label']) {
?>		</label>
<?php }
?>
	</div>