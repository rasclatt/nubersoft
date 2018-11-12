<?php
$dataComp	=	$this->getPluginContent('layout_image');
?>
<img src="<?php echo $this->dec($dataComp['file_path'].$dataComp['file_name']) ?>" alt="<?php echo strip_tags($this->dec($dataComp['content'])) ?>" class="component-view" id="component-<?php echo $dataComp['ID'] ?>" />
