<?php
$compData	=	$this->getPluginContent('editor_div');
$type		=	$compData['component_type'];
$slug		=	$compData['title'];
$title		=	(!empty($slug))? $slug : $type;
$ID			=	$compData['ID'];
?>

	<div class="editor-component type-div">
		<?php include(__DIR__.DS.'common_form.php') ?>
	</div>
