<?php
if(!$this->isAdmin())
	return false;
?>
<div id="admincontent" class="col-count-3 offset">
	<!-- ADMIN TOOL BAR CONTENT -->
	<div class="col-1 span-3 top-bar">
		<?php echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\InspectorPallet')->execute(array('ID'=>$this->getPage('ID'))) ?>
	</div>
	<div class="col-1 span-3 col-count-3 offset">
		<!-- ADMIN TOOLS PLUGIN BUTTONS -->
		<div class="admintools-plugins span-3" id="admin-tool-block">
			<?php echo $this->useTemplatePlugin('button_user_deck') ?>
			<div class="vert-divider"></div>
			<?php echo $this->useTemplatePlugin('admintool_user_buttons') ?>
		</div>
		<div class="col-1 span-3" id="admin-body-block">
			<?php echo $this->useTemplatePlugin('admintool_layouts',"loggedin.php") ?>
		</div>
	</div>
	<div class="col-2" id="admin-footer-block">
		<?php echo $this->render($this->getBackEnd('foot.php')) ?>
		<?php if($this->isAdmin()) { ?>
		<span class="nListener" data-instructions='{"action":"nbr_get_email_receipt_count"}'></span>
		<?php } ?>
	</div>
</div>