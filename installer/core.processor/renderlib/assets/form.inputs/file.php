<?php
	if(!function_exists('AutoloadFunction'))
		return;
		
	AutoloadFunction("download_encode");
	$table		=	nApp::getDefaultTable();
	$salt		=	(!empty(nApp::getFileSalt()))? nApp::getFileSalt() : false;
	$is_valid	=	false;
	if(!empty($values['file_path'])) {
			$fName 		=	$values['file_path'].$values['file_name'];
			$is_valid	=	(is_file(NBR_ROOT_DIR.$fName))? true : false;
		}
	$fId	=	(!empty($values['ID']))? $values['ID']:false;
	$file	=	($values != 'head' && isset($values[$column]))? $values[$column]:false;
?>
	<div class="form-input">
		<?php if(!empty($settings['label'])) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<div class="base_button"><input type="file" name="file[]" /></div>
		<?php if(!empty($settings['label'])) { ?></label><?php } ?>
		<?php
		if($is_valid) { ?>
		<div style="display: inline-block; width: 100%;">
		<div style="display: inline-block;" data-gopage="image.edit" data-gopagekind="g" data-gopagesend="id=<?php echo $fId; ?>&table=<?php echo $table; ?>" class="div_button ajaxtrigger">EDIT IMAGE</div>
			<a class="div_button" href="/download.php?file=<?php echo download_encode(array("file_id"=>$fId,"table_id"=>$table),$salt); ?>">DOWNLOAD</a>
		</div>
		<?php } ?>
	</div>