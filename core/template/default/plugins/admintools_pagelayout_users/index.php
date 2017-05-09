<?php
/*
**	@description	Using the naming convention: admintools_pagelayout_{tablename} You change
**					the look of how a table is displayed
*/

# This is a duplicate script of the main contents found in the normal table view on page:
# /core/template/default/plugins/admintool_layouts/loggedin.php
$this->saveIncidental('template_alert',array('msg'=>'This page is currently generated using a custom page layout. You can change and edit this table file (or others) by viewing this file: '.$this->stripRoot(__FILE__)));
?>
<div style="margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_body') ?>
</div>
<div style="max-width: 1200px; margin: 0 auto;">
	<?php echo $this->useTemplatePlugin('admintools_sql_master') ?>
</div>