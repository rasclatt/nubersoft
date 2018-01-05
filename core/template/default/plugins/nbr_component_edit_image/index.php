<?php
if(!$this->isAdmin())
	return;

$nForm	=	$this->getHelper('nForm');
$POST	=	$this->getPost('deliver');
$ID		=	$POST->ID;
$table	=	(isset($POST->table))? $POST->table : 'components';
$file	=	$this->nQuery()
				->query("select `file_path`,`file_name` from `{$table}` where `ID` = :0",array($ID))
				->getResults(true);
$refer	=	$POST->jumppage;
?>
<span class="nbr_component_single_editor nbr_ux_element">
	
	<div style="display: inline-block; float: right;">
		<?php echo $nForm->open(array('action'=>$refer)) ?>
			<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_make_path_viewable')) ?>
			<?php echo $nForm->fullhide(array('name'=>'ID','value'=>$ID)) ?>
			<?php echo $nForm->fullhide(array('name'=>'table','value'=>$table)) ?>
			<div class="nbr_button" style="margin: 0;">
				<input type="submit" value="READABLE" style="font-size: 16px; padding: 5px; margin: 0;" />
			</div>
		<?php echo $nForm->close() ?>
	</div>
	<h1>Edit Attached Image</h1>
	<div style="display: block; padding: 10px; text-align: left;">
		<h3 style="margin: 0 0 10px 0;">Rename</h3>
		<div id="nbr_file_path" style="display: inline; font-size: 20px; color: #666;"><?php echo $file['file_path'] ?></div><div id="nbr_file_name" onclick="this.contentEditable=true" style="display: inline; font-size: 20px; background-color: #FFF; padding: 5px; border: 1px solid #333; color: #666; text-align: right;"><?php echo pathinfo($file['file_name'],PATHINFO_FILENAME) ?></div><div id="nbr_file_ext" style="display: inline; font-size: 20px; color: #666;">.<?php echo pathinfo($file['file_name'],PATHINFO_EXTENSION) ?></div>
		<div id="nbr_new_file_name"></div>
		<div style="margin-top: 20px;">
			<h3 style="margin: 0 0 10px 0;">Delete File?</h3>
			<span><div id="nbr_checkbox_set" class="nbr_fancy_box"></div></span>
			<p style="display: inline;">Check to remove image from component.</p>
		</div>
	</div>
	<div id="nbr_save_name" style="display: inline-block;">
		<div class="nbr_general_form" style="margin: 0; padding: 0; display: inline-block;">
			<?php echo $nForm->open(array('action'=>$refer)) ?>
				<?php echo $nForm->fullhide(array('name'=>'filename')) ?>
				<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_edit_component_filename','id'=>'nbr_edit_comp_action')) ?>
				<?php echo $nForm->fullhide(array('name'=>'ID','value'=>$ID)) ?>
				<?php echo $nForm->fullhide(array('name'=>'table','value'=>$table)) ?>
				<div class="nbr_fancy_checkbox" style="display: none;">
					<input type="checkbox" name="delete" value="on" id="nbr_delete_checkbox" />
				</div>			
				<div class="nbr_button">
					<?php echo $nForm->submit(array('name'=>'SAVE','value'=>'UPDATE', 'class'=>'nbr_ok')) ?>
				</div>
			<?php echo $nForm->close() ?>
		</div>
	</div>
</span>
<script>
$('#nbr_checkbox_set').on('click',function(){
	var	formAction	=	$('#nbr_edit_comp_action');
	var	instAct		=	['nbr_edit_component_filename','nbr_delete_component_filename'];
	var	psuedoBox	=	$(this);
	var	checkBox	=	$("#nbr_delete_checkbox");
	if(checkBox.is(":checked")) {
		formAction.val(instAct[0]);
		psuedoBox.html('');
		psuedoBox.removeClass("checked");
		checkBox.prop("checked",false);
	}
	else {
		formAction.val(instAct[1]);
		psuedoBox.addClass("checked");
		psuedoBox.html('&#252;');
		checkBox.prop("checked",true);
	}
});

var	textFeild	=	$('#nbr_file_name');
var	newName		=	textFeild.text();
var	formField	=	$('input[name=filename]');
formField.val(newName);
textFeild.on('keyup',function(e) {
	var submitBtn	=	$('#nbr_save_name');
	newName	=	textFeild.text();
	if(newName == '')
		submitBtn.hide();
	else {
		var skipRep	=	[37,38,39,40,8,16];
		var strReplace	=	newName.replace(/[^a-zA-Z0-9\_\-]/g,'_');
		$('#nbr_new_file_name').html('<h3 style="font-family: Courier; padding: 5px; border: 1px dashed #CCC; background-color: #FFF;">"'+strReplace+'"</h3>');
		if(!in_array(skipRep,e.keyCode)) {
			formField.val(strReplace);
		}
		if(!submitBtn.is(":visible"))
			submitBtn.fadeIn();
	}
});
</script>