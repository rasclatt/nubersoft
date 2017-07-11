<?php
$menu		=	$this->getHelper('Methodize')->getMenu();
$Form		=	$this->getPlugin('\nPlugins\Nubersoft\Form');
$content	=	$menu->getContent();
?>
<div class="componentSetWrap" style="background-image: url(<?php echo $this->imagesUrl('/core/mesh.png') ?>); background-repeat: no-repeat; background-size: cover; background-position: center;height: 115px ">
	<div class="nbr_comp_elems"><img src="<?php echo $this->imagesUrl('/core/icn_cont.png') ?>" alt=" " style='max-height: 22px;' /></div>
	<div class="component_block nTrigger" data-instructions='{"FX":{"acton":["#menu_pop"],"fx":["fadeIn"]}}'>
		<table style=" display: block;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					<img src="<?php echo $this->imagesUrl('/core/led_green.png') ?>" alt=" " style='float: left; width: 20px;' />

				</td>
				<td style="text-align: center; ">
					<img src="<?php echo $this->imagesUrl('/core/icn_code.png') ?>" alt=" " style='width: 25px;' />
				</td>
			</tr>
		</table>
	</div>
	<div class="dragonit">
		<div class="templatePopup" style="min-width: 400px;" id="menu_pop">
			<div class="form_window_bar30px">
				<div class="closer_button nTrigger nodrag" data-instructions='{"FX":{"acton":["#menu_pop"],"fx":["fadeOut"]}}'></div>
				<div class="component_header">COMPONENT SETTINGS</div>
			</div>
			
			<div class="nodrag" style="background-color: rgba(0,0,0,0.0); background-image: none; width: 100%;">
				<div class="SubMenuPopUp" style=" display: block;">
					<div class="nbr_general_form">
						<?php echo $Form->open(['class'=>'nbr_menu_edit_form']) ?>
							<?php echo $Form->fullhide(['value'=>'nbr_edit_table_row','name'=>'action']) ?>
							<?php echo $Form->fullhide(['value'=>$menu->ID(),'name'=>'ID']) ?>
							<?php echo $Form->fullhide(['value'=>$menu->getUniqueId(),'name'=>'unique_id']) ?>
							<a href="<?php echo $this->localeUrl('?action=nbr_load_single_editor&cId='.$menu->ID()) ?>" target="_blank" id="nbr_menu_edit_larger" class="nbr_button" style="font-size: 14px; padding: 8px; font-family: Arial, Helvetica, sans-serif;">EDIT LARGER</a>
							<?php echo $Form->textarea(['value'=>$content,'name'=>'content','class'=>'tabber','id'=>'nbr_menu_edit_comp','other'=>['style="font-family: \'Lucida Console\', Monaco, monospace;"']]) ?>
							<div class="nbr_button">
								<?php echo $Form->submit(['value'=>'SAVE','name'=>'update','id'=>'nbr_menu_edit_comp_sumbit']) ?>
							</div>
						<?php echo $Form->close() ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
/*
**	@description	This is just a way to disable the current component in favor of loading the component
**					to a larger version for editing
*/
$(document).ready(function() {
	$("#nbr_menu_edit_larger").on('click',function() {
		$("#nbr_menu_edit_comp").prop('disabled',true);
		$("#nbr_menu_edit_comp_sumbit").prop('disabled',true).val('RELOAD PAGE').hide();
		var	getLButton		=	$('#nbr_menu_edit_larger');
		var	getStyleAttr	=	getLButton.attr('style');
		getLButton.replaceWith('<a href="<?php echo $this->localeUrl($this->getPageURI('full_path')) ?>" class="nbr_button small" style="'+getStyleAttr+'">RELOAD</a>');
	});
});
</script>