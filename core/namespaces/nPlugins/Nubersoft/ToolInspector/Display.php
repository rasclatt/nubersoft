<?php
$this->useTable('main_menus');
$nProccessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','component');
$nForm			=	$this->getHelper('nForm');
$action			=	((isset($this->getDataNode('_SERVER')->HTTP_REFERER))? $this->getDataNode('_SERVER')->HTTP_REFERER : '#');
$this->saveSetting('nProcessor', $nProccessor);
// Determine if the component is new or old
$function			=	($CompSet)? 'update': 'add';
// Determine if it's been admin locked
$echoField	=	(!empty($this->data['admin_lock']))? $this->isAdmin() : true;
$ID			=	(isset($this->data['ID']))? $this->data['ID'] : false;
$unique_id	=	(isset($this->data['unique_id']))? $this->data['unique_id'] : false;
$ref_type	=	'parent_id';
$is_comp	=	false;
$parent		=	(isset($this->data['parent_id']))? $this->data['parent_id'] : false;
$runAction	=	$this->getActionType();
if(isset($this->data['parent_id']))
	$this->data['page_options']	=	json_decode($this->safe()->decode($this->data['page_options']));
?>
	<div id="confirm_modal_<?php echo $ID ?>" class="modal_slide_down nbr_ux_element"></div>
<?php
	if(!empty($this->data['ID'])) {
?>
	<!-- Component buttons -->
	<div style="width: 98%; padding: 1%; display: inline-block;" class="nbr_ux_element">
		<?php
		//echo $this->addNewComponent($CompSet);
		echo $this->deleteComponent($CompSet);
		//echo $this->duplicateComponent();
		?>
	</div>
<?php
	}
?>
	<div class="nbr_component_wrap nbr_general_form"><?php if($echoField) { ?>
		<?php echo $nForm->open(array('action'=>$action, 'enctype'=>"multipart/form-data")) ?>
			<?php echo $nForm->fullhide(array('name'=>'action','value'=>$this->getActionType())) ?>
			<?php echo $nForm->fullhide(array('name'=>"token[nProcessor]",'value'=>$nProccessor)) ?>
			<?php echo $nForm->fullhide(array('name'=>'ID','value'=>$ID)) ?>
			<?php echo $nForm->fullhide(array('name'=>'unique_id','value'=>$unique_id)) ?>
			<div class="form-input">
				<div style="display: inline-block; width: 100%;">
					<?php echo $this->containerDropDown($parent,$unique_id,false) ?>
				</div>
				<?php echo $this->createFormElements() ?>
			</div>
			<?php
			if(isset($this->data['ref_page'])) { ?>
			<?php echo $nForm->fullhide(array('name'=>'ref_page','value'=>$ref_page)) ?>
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
<script>
$("input[type=submit]").removeAttr('disabled');
</script>