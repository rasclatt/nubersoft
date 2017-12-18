<?php
# This is retrieved by xml file in the /client/plugins/.*
if(!empty($this->getDataNode('pageURI_redirect'))) {
	# Render the file
	echo $this->render($this->getDataNode('pageURI_redirect'));
}
# Create $_GET plugin include
elseif(!empty($this->getGet('requestTable')) && $this->templatePluginExists($plugin = 'admintools_pagelayout_'.$this->getGet('requestTable'))) { ?>
	<div style="display: block; overflow: auto;">
		<?php  echo $this->useTemplatePlugin($plugin) ?>
	</div>
	<?php
}
else {
?>
<div class="col-1 span-3">
	<?php
	if(!empty($this->getGet('requestTable'))) { ?>
	<div style="display: block; overflow: auto;">
		<?php echo $this->useTemplatePlugin('admintools_body') ?>
	</div>	
	<?php
	}
	else {
	?>
	<div style="text-align: left; border: 1px solid #CCC; padding: 30px; margin: 30px 30px;">
		<h1 class="nbr_ux_element">Back Office</h1>
	</div>
	<?php } ?>
</div>
<?php if(!empty($this->getGet('requestTable'))) { ?>

<div class="col-2">
	<?php echo $this->useTemplatePlugin('admintools_sql_master') ?>
</div>

<?php
	}
}
?>

<div class="col-2">
	<?php echo $this->useTemplatePlugin('admintools_warning_display') ?>
</div>

<div class="col-2">
	<?php echo $this->useTemplatePlugin('admintools_error_display') ?>
</div>