<?php
$compData	=	$this->getPluginContent('editor_contact_form');
$type		=	$compData['component_type'];
$slug		=	$compData['title'];
$title		=	(!empty($slug))? $slug : $type;
$ID			=	$compData['ID'];
?>

	<div class="editor-component type-contact_form">
		<?php include(__DIR__.DS.'common_form.php') ?>
	</div>
