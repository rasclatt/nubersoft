<div class="col-count-5 gapped lrg-3 gapped med-2 gapped sml-1">
	<?php
	$layout	=	@$this->Settings_Page_View()->create($this->getPage('unique_id'), 'editor');
	echo (!empty($layout))? $layout : '<div class="component-add-new">'.$this->getPlugin('component', DS.'add.php').'</div>';
	?>
</div>