<?php
$hasRedirect	=	(!empty($this->getDataNode('pageURI_redirect')));
$hasCustomPg	=	(!empty($this->getGet('requestTable')) && $this->templatePluginExists($plugin = 'admintools_pagelayout_'.$this->getGet('requestTable')));
$hasReqTable	=	(!empty($this->getGet('requestTable')));

# This is retrieved by xml file in the /client/plugins/.*
if($hasRedirect):
	# Render the file
	echo $this->render($this->getDataNode('pageURI_redirect'));
# Create $_GET plugin include
elseif($hasCustomPg):?>
	<div class="col-1 span-3"><?php  echo $this->useTemplatePlugin($plugin) ?></div>
	<?php
else:
	if($hasReqTable): ?>
<div class="col-1 span-3">
	<div id="admin-body"><?php echo $this->useTemplatePlugin('admintools_body') ?></div>
</div>
<div class="col-2" id="admin-sql-master">
	<?php echo $this->useTemplatePlugin('admintools_sql_master') ?>
</div>
	<?php
	else:
	?>
<div class="col-1 span-3">
	<div id="admin-backoffice">
		<h1 class="nbr_ux_element">Back Office</h1>
		<p>Welcome to the admin. Your Admin home page can be edited as you please.</p>
	</div>
</div>
	<?php
	endif;
endif;
	?>
<div class="col-2 span-1">
	<?php echo $this->useTemplatePlugin('admintools_warning_display') ?>
</div>