<?php
# Check if this component contains an active data set
$CompSet		=	(isset($this->data['unique_id']));
$nProccessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','component');
$nForm			=	$this->getHelper('nForm');
$Form			=	$this->getPlugin('nPlugins\Nubersoft\Form');
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
	
//	$this->nQuery()->query("INSERT INTO `component_locales` (page_live,comp_id) VALUES ('on',79)");
	if(!empty($this->data['ID'])) {
		$getLocales	=	$this->arrayKeys($this->organizeByKey($this->nQuery()
			->query("SELECT `locale_abbr` FROM `component_locales` WHERE `comp_id` = :0 AND `page_live` = 'on'",array($this->data['ID']))
			->getResults(),'locale_abbr'));
			
			$unique	=	uniqid();
?>
	<div style="display: inline-block; min-width: 340px; width: 100%; margin-bottom: 10px;">
		<div class="toolsheaders nTrigger" data-instructions='{"FX":{"acton":[".nbr_tools_headers_panels","next::accordian"],"fx":["slideUp","slideToggle"],"fxspeed":["fast","fast"]}}'><?php echo $this->getHelper('nImage')->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'locales.png',array('style'=>'max-height: 20px;')) ?>Locales</div>
		<div class="nbr_tools_headers_panels">
			<div style="padding: 10px;">
				<div class="nbr_tool_component_contain" style="color: #FFF;">
					<?php echo $Form->open(array('class'=>'change_component_locales_form')) ?>
						<div style="width: 100%; display: inline-block; font-size: 14px; margin: 5px 0;">
							<label id="component_locale_click<?php echo $this->data['ID'].$unique ?>">
								<input type="checkbox" />TOGGLE OFF/NO
							</label>
						</div>
						<?php echo $Form->fullhide(array('name'=>'action','value'=>'nbr_edit_component_locale')) ?>
						<?php echo $Form->fullhide(array('name'=>'ID','value'=>$this->data['ID'])) ?>
						<?php 
						$cou	=	$Form->createOptions('locale_abbr')->fetchOpts();
						foreach($cou as $option) {
							$settings	=	array(
								'name'=>'component_locales[]',
								'value'=>$option['value'],
								'label'=>false,
								'wrap'=>false,
								'class'=>'component_check_all',
								'selected'=>(is_array($getLocales) && in_array($option['value'],$getLocales))
							);
							echo '<label style="font-size: 14px; display: inline-block; width: 48%;">'.strip_tags($Form->checkbox($settings),'<input>').'&nbsp;'.$option['name'].'</label>';
						}
						?>
						<div style="width: 100%; display: inline-block; margin: 30px 0 0px 0">
							<div class="nbr_button small">
								<?php echo $Form->submit(array('value'=>'SAVE')) ?>
							</div>
						</div>
					<?php echo $Form->close() ?>
				</div>
			</div>
		</div>
	<script>
	// Convenient check-all
	$('#component_locale_click<?php echo $this->data['ID'].$unique ?>').on('click',function(e) {
		var getCurrChecked	=	$('#component_locale_click<?php echo $this->data['ID'].$unique ?>');
		var getCurrChStat	=	getCurrChecked.find('input[type="checkbox"]').prop("checked");
		getCurrChecked.parents('.change_component_locales_form').find('input[name=component_locales\\[\\]]').prop("checked",getCurrChStat );
	});
	</script>
	<?php
	}
	
	if(!empty($this->data['ID'])) {
		$file	=	($runAction =! 'nbr_save_menu')? $this->data['file_path'].$this->data['file_name'] : false;
	?>
	<div class="nbr_component_wrap nbr_general_form nbr_ux_element" style="margin-top: 30px;"><?php if($echoField) { ?>
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