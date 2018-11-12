<?php
$Form		=	$this->getHelper('nForm');
$Token		=	$this->getHelper('nToken');
$compData	=	$this->getPluginContent('add_component');
$token		=	(!empty($compData['token']))? $compData['token'] : 'component_add';
$type		=	(!empty($compData['component_type']))? $compData['component_type'] : 'code';

?>
<?php echo $Form->open() ?>
	<?php echo strip_tags($Form->fullhide(['name' => 'action', 'value' => 'edit_component']),'<input>') ?>
	<?php echo strip_tags($Form->fullhide(['name' => 'subaction', 'value' => 'add_new']),'<input>') ?>
	<?php echo strip_tags($Form->fullhide(['name' => 'token[nProcessor]', 'value' => $Token->setToken($token)->getToken($token, false)]),'<input>') ?>
	<?php echo strip_tags($Form->fullhide(['name' => 'ref_page', 'value' => $compData['ref_page']]),'<input>') ?>
	<?php echo strip_tags($Form->fullhide(['name' => 'parent_type', 'value' => $type]),'<input>') ?>
	<?php echo strip_tags($Form->submit(['value' => 'ADD'.((empty($compData))? ' COMPONENT' : '' ), 'class' => ((empty($compData))? 'medi' : 'mini' ).'-btn dark no-margin']),'<input>') ?>
<?php echo $Form->close() ?>