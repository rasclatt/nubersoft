<?php

$nProccessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','component');
$nForm			=	$this->getHelper('nForm');
$action			=	((isset($this->getDataNode('_SERVER')->HTTP_REFERER))? $this->getDataNode('_SERVER')->HTTP_REFERER : '#');
$this->saveSetting('nProcessor', $nProccessor);
// Determine if the component is new or old
$function			=	($CompSet)? 'update': 'add';
// Determine if it's been admin locked
$echoField	=	(!empty($this->data['admin_lock']))? $this->isAdmin() : true;
?>
	<div id="confirm_modal_<?php echo $this->data['ID'] ?>" class="modal_slide_down nbr_ux_element"></div>
	<!-- Component buttons -->
	<div style="width: 98%; padding: 1%; display: inline-block;" class="nbr_ux_element">
<?php
	$editImgButton	=	$this->alterAttachedImage();
	$addButton		=	$this->addNewComponent($CompSet);
	$deleteButton	=	$this->deleteComponent($CompSet);
	$dupButton		=	$this->duplicateComponent();
	$ref_type		=	(!isset($this->data['ref_page']))? 'parent_id' : 'ref_page';
	$is_comp		=	(isset($this->data['ref_page']));
	$parent			=	$this->data[$ref_type];
	$runAction		=	$this->getActionType();
	
	echo $editImgButton;
	echo (!empty($this->data['ID']))? $addButton : '';
	echo $deleteButton;
	echo $dupButton;
?>
	</div>
	<?php
	if(!empty($this->data['ID'])) {
		$file	=	($runAction =! 'nbr_save_menu')? $this->data['file_path'].$this->data['file_name'] : false;
	?>
	<div class="nbr_component_wrap nbr_general_form nbr_ux_element"><?php if($echoField) { ?>
		<?php echo $nForm->open(array('action'=>$action, 'enctype'=>"multipart/form-data")) ?>
			<?php echo $nForm->fullhide(array('name'=>'action','value'=>$this->getActionType())) ?>
			<?php echo $nForm->fullhide(array('name'=>"token[nProcessor]",'value'=>$nProccessor)) ?>
			<?php echo $nForm->fullhide(array('name'=>'ID','value'=>((isset($this->data['ID']))? $this->data['ID'] : ''))) ?>
			<?php echo $nForm->fullhide(array('name'=>'unique_id','value'=>((isset($this->data['unique_id']))? $this->data['unique_id'] : ''))) ?>
			<?php if($is_comp) { ?>
			<?php echo $nForm->fullhide(array('name'=>'action_options[thumb]','value'=>true)) ?>
			<?php echo $nForm->fullhide(array('name'=>'action_options[filter]','value'=>false)) ?>
			<?php } ?>
			<div class="form-input">
				<div style="display: inline-block; width: 100%;">
					<?php echo $this->containerDropDown($parent,$ref_type,$is_comp) ?>
				</div>
				<?php echo $this->createFormElements() ?>
			</div>
			<?php
			if(isset($this->data['ref_page'])) { ?>
			<?php echo $nForm->fullhide(array('name'=>'ref_page','value'=>$this->data['ref_page'])) ?>
			<?php
			}
			?>
			<div class="nbr_button">
				<?php echo $nForm->submit(array('name'=>$function,'value'=>strtoupper($function),'disabled'=>"disabled",'style'=>"margin: 15px auto 0 auto;")) ?>
			</div>
		<?php echo $nForm->close() ?>			
		<?php
		}
		else {
		?>
				Component Locked: <br />You must be a Superuser to Unlock.
		<?php
		}
		?>
	</div>
<?php
}
else {
?>
	<div style="" class="nbr_ux_element">
		<div class="nbr_component_add_instr">Click </div><?php echo $addButton ?><div class="nbr_component_add_instr"> to ADD<br />a new component.</div>
	</div>
<?php
}
?>
<script>
$("input[type=submit]").removeAttr('disabled');
</script>