<?php
$count_dirs	=	count(array_filter(explode("/",$useData['full_path'])));
if(empty($this->getDataNode('main_menu_dropdowns')))
	$this->saveSetting('main_menu_dropdowns',$this->toArray($this->getDropDowns("main_menus")));

$dropdowns		=	$this->toArray($this->getData()->getMainMenuDropdowns());
$template		=	(isset($useData['template']))? $useData['template'] : '';
$dropTemplate	=	(isset($dropdowns['template']))? $dropdowns['template'] : false;
?>
<div class="nbr_tool_dir<?php if($count_dirs > 1) echo "_multi"; ?>">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="nbr_pagelive_led">
				<?php echo $this->renderToggleIcon($useData['page_live']); ?>
			</td>
			<td class="nbr_tool_fullpath_link">
				<p>
					<a href="<?php echo $useData['full_path']; ?>"><span style="font-size: 16px;<?php $col = ($count_dirs > 1)? ' color: #999; text-shadow: 1px 1px 2px #000;':''; echo $col; ?>"><?php echo preg_replace('/[^0-9a-zA-Z\.\-\_]/','',ucwords($this->safe()->decode($useData['menu_name']))); ?></span></a>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="nbr_tools_miniedit">
				<?php	$IDPanel	=	'#quickedit'.$useData['ID'].'_panel' ?>
				<div class="showsub nTrigger" id="quickedit<?php echo $useData['ID']; ?>" data-instructions='{"FX":{"acton":["<?php echo $IDPanel ?>"],"fx":["toggleClass"]}}'>
					<div class="quickfullpath">EDIT: <?php echo $useData['full_path']; ?></div>
				</div>
				<div class="nbr_tool_panel_wrap displayNone" data-subfx='{"toggleClass":{"data":["displayNone"]}}' id="quickedit<?php echo $useData['ID']; ?>_panel">
					<?php $Form	=	$this->getForm(); ?>
					<?php echo $Form->open(array('enctype'=>'multipart/form-data')); ?>
						<?php echo $Form->fullhide(array('name'=>'action','value'=>$this->getActionType())) ?>
						<?php echo $Form->fullhide(array('name'=>'token[nProcessor]','value'=>$this->getHelper('nToken')->setMultiToken('nProcessor','nbr_update_menu'))); ?>
						<?php echo $Form->fullhide(array('name'=>'action','value'=>'nbr_save_menu')); ?>
						<?php echo $Form->fullhide(array('value'=>$useData['ID'],'name'=>'ID')); ?>
						<?php echo $Form->fullhide(array('value'=>$useData['unique_id'],'name'=>'unique_id')); ?>
						<div class="fieldsGrad tools-mini-edit">
							<label for="template">
							Template
							<?php echo $Form->select(array('value'=>$template,'name'=>'template','options'=>$Form->formatSelectOptions($dropTemplate,$template))); ?>
							</label>
							
							<label for="link">
							<?php echo $Form->text(array('value'=>$useData['link'],'name'=>'link','style'=>'width: 80px;')); ?>
							</label>
							<label for="page_order">
							Menu Order (If in menu)
							<?php echo $Form->text(array('value'=>$useData['page_order'],'name'=>'page_order','style'=>'width: 40px;')); ?>
							</label>
						</div>
						<div class="tools-mini-edit">
							<?php if($useData['is_admin'] != 2 && $useData['is_admin'] != 1) { ?>
							<label for="parent_id">
								Parent Folder
								<div style="display: inline-block; width: 100%;">
									<?php echo $this->containerDropDown($useData['unique_id']); ?>
								</div>
							</label>
							<?php } ?>
							<label for="session_status">
							<?php echo $this->renderToggleIcon($useData['session_status']); ?>&nbsp;Login Required?
							<?php echo $Form->select(array('value'=>$useData['session_status'],'name'=>'session_status','style'=>'width: 80px;','options'=>$Form->formatSelectOptions($dropdowns['session_status'],$useData['session_status']))); ?>
							</label>
							<label for="usergroup">
							User Group
							<?php echo $Form->select(array('value'=>$useData['usergroup'],'name'=>'usergroup','style'=>'width: 80px;','options'=>$Form->formatSelectOptions($dropdowns['usergroup'],$useData['usergroup']))); ?>
							</label>
							<label for="auto_cache">
							<?php echo $this->renderToggleIcon($useData['auto_cache']); ?>&nbsp;Page-Cached Status
							<?php echo $Form->select(array('value'=>$useData['auto_cache'],'name'=>'auto_cache','style'=>'width: 80px;','options'=>$Form->formatSelectOptions($dropdowns['auto_cache'],$useData['auto_cache']))); ?>
							</label>
							<label for="page_live">
							<?php echo $this->renderToggleIcon($useData['page_live']); ?>&nbsp;Page-Live Status
							<?php echo $Form->select(array('value'=>$useData['page_live'],'name'=>'page_live','style'=>'width: 80px;','options'=>$Form->formatSelectOptions($dropdowns['page_live'],$useData['page_live']))); ?>
							</label>
						</div>
						<div class="nbr_button">
							<?php echo $Form->submit(array("disabled"=>"disabled","type"=>"submit","name"=>"update","value"=>"SAVE",'class'=>'disabled-submit')); ?>
						</div>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>