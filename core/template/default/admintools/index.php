<?php
/*
**	Set some preferences for use down the page
**	$frontEnd	=	$this->getFrontEnd();
*/

//echo printpre($this->getDataNode('workflow_run'));
//$this->nQuery()->query("update users set `user_status` = 'on' where `username` = 'ryan@mxicorp.com'");

$thisObj	=	$this;
$cacheBase	=	$this->getStandardPath();
$headCache	=	$cacheBase.DS.'header.html';
$footCache	=	$cacheBase.DS.$this->isLoggedIn().DS.'footer.html';
$layout		=	($this->isAdmin())? 'in' : 'out';
# Include some frontend/backend files
include($this->getBackEnd(DS.'inclusions'.DS.'config.admin.php'));
# Just make sure user is on whitelist if set to use
$this->useTemplatePlugin('whitelist');

if(is_file($headCache))
	unlink($headCache);

echo $this->getTemplateDoc('head.php','admintools');
# Render/cached head block
//echo $this->cacheLayout($headCache,function($thisObj) {
//	return $thisObj->getHeader('admintools');
//});
# Save my data if not yet done
$this->getHelper('CoreMySQL')->saveDatabaseScheme(NBR_ROOT_DIR.DS.'installer'.DS.'sql');
?>
<body class="nbr_admintools<?php if($this->isAdmin()) echo '_loggedin' ?>">
	<!-- MODAL -->
	<div id="loadspot_modal"></div>
	<!-- CONTENT -->
	<div id="content" class="nbr_wrapper">
		<!-- ADMIN CONTENT -->
		<div id="admincontent" class="col-count-3 offset">
			<?php
				if($this->isAdmin()) {
			?>
			
			<!-- ADMIN TOOL BAR CONTENT -->
			<div class="col-1 span-3 top-bar">
				<?php echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\InspectorPallet')->execute(array('ID'=>$this->getPage('ID'))) ?>
			</div>
			
			<?php } ?>
			
			<div class="col-1 span-3">
				<?php
				if($this->isAdmin()) {
				?>
				
				<!-- ADMIN TOOLS PLUGIN BUTTONS -->
				<div class="admintools-plugins">
					<?php echo $this->useTemplatePlugin('button_user_deck') ?>
					
					<div class="vert-divider"></div>
					
					<?php echo $this->useTemplatePlugin('admintool_user_buttons') ?>

				</div>
				<!-- END ADMIN TOOLS PLUGIN BUTTONS -->
				<?php
				}
				?>
				
				<!-- ADMIN TOOLS LAYOUT -->
				
				<?php echo $this->useTemplatePlugin('admintool_layouts',"logged{$layout}.php") ?>
				
				<!-- END ADMIN TOOLS LAYOUT -->
			</div>
		</div>
		<!-- END ADMIN CONTENT -->
		
	</div>
	<div>
		<?php
		if($this->isLoggedIn()) { ?>
		<div id="foot_cache_block">
		<?php } ?>
			<div class="nbr_foot">
				<?php echo $this->render(__DIR__.DS.'foot.php'); if($this->isAdmin()) echo 'My Ip: '.$this->getClientIp().'. Database: "'.$this->getDbName().'"' ?>
			</div>
		<?php
		if($this->isLoggedIn()) { ?>
		</div>
		<?php } ?>
	</div>
	<?php if($this->isAdmin()) { ?>
	<span class="nListener" data-instructions='{"action":"nbr_get_email_receipt_count"}'></span>
	<?php } ?>
</body>
</html>