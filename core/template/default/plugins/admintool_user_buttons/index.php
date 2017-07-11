<?php
if($this->getCurrentGroup() != 'NBR_SUPERUSER')
	return ?>
<?php echo $this->useTemplatePlugin('button_deploy_changes') ?>