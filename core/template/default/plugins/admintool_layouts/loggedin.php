<?php
# This is retrieved by xml file in the /client/plugins/.*
if(!empty($this->getDataNode('pageURI_redirect'))) {
	# Render the file
	echo $this->render($this->getDataNode('pageURI_redirect'));
}
# Create $_GET plugin include
elseif(!empty($this->getGet('requestTable')) && $this->templatePluginExists($plugin = 'admintools_pagelayout_'.$this->getGet('requestTable')))
	echo $this->useTemplatePlugin($plugin);
else {
?>
<div style="margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_body') ?>
</div>
<div style="max-width: 1200px; margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_sql_master') ?>
</div>
<?php
}
?>
<div style="max-width: 1200px; margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_warning_display') ?>
</div>
<div style="max-width: 1200px; margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_error_display') ?>
</div>