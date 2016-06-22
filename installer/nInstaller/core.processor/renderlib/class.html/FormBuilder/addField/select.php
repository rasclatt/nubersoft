<?php
	$input					=	array();
	$input['class']			=	"";
	
	if(!empty($opts['class'])) {
			$input['class']	=	(!empty($opts['class']) && is_array($opts['class']))? ' class="'.implode(" ",$opts['class']).'"':' class="'.$opts['class'].'"';
		}
		
	$input['id']				=	(!empty($opts['title']))? ' id="'.$opts['id'].'"' : false;
	$input['title']			=	(!empty($opts['title']))? $opts['title'] : false;
	$input['label']			=	(!empty($opts['label']))? $opts['label'] : false;
	$input['values']			=	(!empty($opts['values']))? $opts['values'] : array();
	$input['size']			=	(!empty($opts['size']))? ((is_numeric($size))? 'size="'.$size.'"':' style="width: '.Safe::decode($size).';"'):"";
	$input['name']			=	(!empty($opts['name']))? $opts['name'] : "untitled_input";
	$input['disabled']		=	(!empty($opts['disabled']))? " disabled" : "";
	$input['selected']		=	(!empty($opts['selected']))? " selected" : "";
	$input['wrapper']		=	(!empty($opts['wrapper']))? $opts['wrapper'] : "form-input";
?>
	<div class="<?php echo $input['wrapper']; ?>">
<?php if($input['label']) {
?>		<label><span class="label-hd"><?php echo $input['label']; ?></span>
<?php 	}
?>		<select name="<?php echo $input['name']; ?>"<?php echo $input['id']; ?><?php echo $input['size']; echo $input['disabled'];echo $input['selected']; echo $input['class']; ?>>
<?php
		foreach($input['values'] as $values => $txt) {
?>			<option value="<?php echo $values; ?>"><?php echo $txt; ?></option>
<?php		}
?>
		</select>
<?php if($input['label']) {
?>		</label>
<?php }
?>
	</div>